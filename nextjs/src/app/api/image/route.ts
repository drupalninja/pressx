import { processImage } from '@/lib/image-processor';
import { NextRequest, NextResponse } from 'next/server';

export async function GET(request: NextRequest) {
  const searchParams = request.nextUrl.searchParams;
  const url = searchParams.get('url');
  const width = parseInt(searchParams.get('width') || '0', 10);
  const height = parseInt(searchParams.get('height') || '0', 10);
  const quality = parseInt(searchParams.get('quality') || '90', 10);

  if (!url || !width || !height) {
    return new NextResponse('Missing required parameters', { status: 400 });
  }

  try {
    const processedImage = await processImage(url, width, height, quality);

    return new NextResponse(processedImage, {
      headers: {
        'Content-Type': 'image/webp',
        'Cache-Control': 'public, max-age=31536000, immutable',
      },
    });
  } catch (error) {
    console.error('Error processing image:', error);
    return new NextResponse('Error processing image', { status: 500 });
  }
}
