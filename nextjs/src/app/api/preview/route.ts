import { NextRequest, NextResponse } from 'next/server';

// Function to get JWT token for authentication
async function getJwtToken() {
  const endpoint = process.env.NEXT_PUBLIC_WORDPRESS_API_URL || 'https://pressx.ddev.site/graphql';
  console.log('Using GraphQL endpoint:', endpoint);

  try {
    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        query: `
          mutation LoginUser {
            login(input: {
              clientMutationId: "nextjs-preview",
              username: "${process.env.WORDPRESS_PREVIEW_USERNAME || 'admin'}",
              password: "${process.env.WORDPRESS_PREVIEW_PASSWORD || ''}"
            }) {
              authToken
            }
          }
        `,
      }),
    });

    const data = await response.json();
    console.log('JWT auth response:', JSON.stringify(data));

    if (data.data?.login?.authToken) {
      return data.data.login.authToken;
    }

    console.error('Failed to get JWT token:', data);
    return null;
  } catch (error) {
    console.error('Error getting JWT token:', error);
    return null;
  }
}

export async function GET(request: NextRequest) {
  const searchParams = request.nextUrl.searchParams;
  const secret = searchParams.get('secret');
  const id = searchParams.get('id');

  // Check the secret and next parameters
  // This secret should be only known to this API route and the CMS
  if (secret !== process.env.WORDPRESS_PREVIEW_SECRET || !id) {
    return new Response('Invalid token', { status: 401 });
  }

  // Get JWT token for authenticated GraphQL requests
  const authToken = await getJwtToken();

  if (!authToken) {
    return new Response('Failed to authenticate with WordPress', { status: 500 });
  }

  // Create a NextResponse object for better cookie handling
  const response = NextResponse.redirect(new URL(`/preview/${id}`, request.url));

  // Set cookies using NextResponse methods
  response.cookies.set('__prerender_bypass', secret, { path: '/' });
  response.cookies.set('__next_preview_data', secret, { path: '/' });
  response.cookies.set('wp_jwt_token', authToken, { path: '/', httpOnly: true });

  console.log('Setting cookies:', response.cookies.getAll());

  return response;
}
