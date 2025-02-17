import sharp from 'sharp';

export async function processImage(
  url: string,
  width: number,
  height: number,
  quality: number = 90
) {
  try {
    const response = await fetch(url);
    const arrayBuffer = await response.arrayBuffer();
    const buffer = Buffer.from(arrayBuffer);

    const processedImage = await sharp(buffer)
      .resize(width, height, {
        fit: 'cover',
        position: 'center'
      })
      .toFormat('webp', { quality })
      .toBuffer();

    return processedImage;
  } catch (error) {
    console.error('Error processing image:', error);
    throw error;
  }
}
