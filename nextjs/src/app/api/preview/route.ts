import { NextRequest } from 'next/server';

export async function GET(request: NextRequest) {
  const searchParams = request.nextUrl.searchParams;
  const secret = searchParams.get('secret');
  const id = searchParams.get('id');

  // Check the secret and next parameters
  // This secret should be only known to this API route and the CMS
  if (secret !== process.env.WORDPRESS_PREVIEW_SECRET || !id) {
    return new Response('Invalid token', { status: 401 });
  }

  // Enable Preview Mode by setting the cookies
  const headers = new Headers();
  headers.append('Set-Cookie', `__prerender_bypass=${secret}; Path=/`);
  headers.append('Set-Cookie', `__next_preview_data=${secret}; Path=/`);

  // Redirect to the path from the fetched post
  // We don't redirect to searchParams.slug as that might lead to open redirect vulnerabilities
  return new Response(null, {
    status: 307,
    headers: {
      ...headers,
      Location: `/preview/${id}`,
    },
  });
}
