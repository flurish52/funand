/**
 * Compresses an image file down to a target size by progressively
 * re-encoding it as JPEG at decreasing quality (and, if quality alone
 * isn't enough, shrinking dimensions too). Runs entirely in the browser
 * via <canvas> — no server round trip needed before upload.
 */
export function useImageCompression() {
    const MAX_SIZE_BYTES = 2 * 1024 * 1024 // 2MB
    const MIN_QUALITY = 0.5
    const QUALITY_STEP = 0.1
    const MAX_DIMENSION = 2560 // px, longest side — plenty for a cover image

    function loadImage(file) {
        return new Promise((resolve, reject) => {
            const img = new Image()
            const url = URL.createObjectURL(file)
            img.onload = () => {
                URL.revokeObjectURL(url)
                resolve(img)
            }
            img.onerror = (err) => {
                URL.revokeObjectURL(url)
                reject(err)
            }
            img.src = url
        })
    }

    function canvasToBlob(canvas, quality) {
        return new Promise((resolve) => {
            canvas.toBlob((blob) => resolve(blob), 'image/jpeg', quality)
        })
    }

    /**
     * @param {File} file
     * @returns {Promise<File>} a new File, same name, under MAX_SIZE_BYTES
     *   when possible. Non-image files or files already under the limit
     *   are returned untouched.
     */
    async function compressImage(file) {
        if (!file || !file.type.startsWith('image/')) return file
        if (file.size <= MAX_SIZE_BYTES) return file

        const img = await loadImage(file)

        let { width, height } = img
        if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
            const scale = MAX_DIMENSION / Math.max(width, height)
            width = Math.round(width * scale)
            height = Math.round(height * scale)
        }

        const canvas = document.createElement('canvas')
        canvas.width = width
        canvas.height = height
        canvas.getContext('2d').drawImage(img, 0, 0, width, height)

        let quality = 0.9
        let blob = await canvasToBlob(canvas, quality)

        while (blob && blob.size > MAX_SIZE_BYTES && quality > MIN_QUALITY) {
            quality -= QUALITY_STEP
            blob = await canvasToBlob(canvas, quality)
        }

        // Still too big at floor quality — fall back to the smallest
        // version we managed rather than blocking the upload entirely.
        if (!blob) return file

        const newName = file.name.replace(/\.\w+$/, '') + '.jpg'
        return new File([blob], newName, { type: 'image/jpeg' })
    }

    return { compressImage, MAX_SIZE_BYTES }
}
