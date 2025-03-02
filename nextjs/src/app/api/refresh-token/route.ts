import { NextRequest, NextResponse } from 'next/server';

/**
 * API route to refresh JWT token.
 * This uses the WordPress GraphQL API to get a new token using stored credentials.
 */
export async function GET(request: NextRequest) {
  try {
    // Check if preview mode is enabled
    const previewMode = process.env.NEXT_PUBLIC_PREVIEW_MODE === 'true';
    if (!previewMode) {
      return NextResponse.json(
        { error: 'Preview mode is not enabled' },
        { status: 403 }
      );
    }

    // Get WordPress URL and credentials
    const wpApiUrl = process.env.NEXT_PUBLIC_WORDPRESS_API_URL;
    const username = process.env.WORDPRESS_PREVIEW_USERNAME;
    const password = process.env.WORDPRESS_PREVIEW_PASSWORD;

    if (!wpApiUrl || !username || !password) {
      return NextResponse.json(
        {
          error: 'Missing configuration',
          debug_info: 'WordPress API URL, username, or password not configured',
        },
        { status: 500 }
      );
    }

    // Get a new JWT token
    console.log('Attempting to get new JWT token from:', wpApiUrl);
    const response = await fetch(wpApiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      cache: 'no-store',  // Prevent caching
      body: JSON.stringify({
        query: `
          mutation LoginUser {
            login(input: {
              clientMutationId: "nextjs-refresh-${Date.now()}",
              username: "${username}",
              password: "${password}"
            }) {
              authToken
              refreshToken
              user {
                id
                name
              }
            }
          }
        `,
      }),
    });

    const data = await response.json();
    console.log('GraphQL response status:', response.status);

    if (!data.data?.login?.authToken) {
      console.error('Failed to get JWT token:', data);

      // Check for specific error types
      let errorMessage = 'GraphQL login mutation failed';
      if (data.errors && data.errors.length > 0) {
        errorMessage = data.errors[0].message || errorMessage;
      }

      return NextResponse.json(
        {
          error: 'Failed to refresh token',
          message: errorMessage,
          debug_info: 'GraphQL login mutation failed',
          graphql_response: data,
          status_code: response.status,
        },
        { status: 500 }
      );
    }

    // Create response with the new token
    const authToken = data.data.login.authToken;
    console.log('Successfully obtained new JWT token');
    const nextResponse = NextResponse.json({
      success: true,
      message: 'Token refreshed successfully',
      user: data.data.login.user?.name || 'Authenticated User',
    });

    // Set the JWT token cookie with a longer expiration (30 days)
    nextResponse.cookies.set('pressxJWT', authToken, {
      path: '/',
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      sameSite: 'lax',
      maxAge: 30 * 24 * 60 * 60,  // 30 days in seconds
    });

    return nextResponse;
  } catch (error) {
    console.error('Error refreshing token:', error);
    return NextResponse.json(
      {
        error: 'Failed to refresh token',
        message: error instanceof Error ? error.message : 'Unknown error',
        error_type: error instanceof Error ? error.constructor.name : typeof error,
        stack: error instanceof Error ? error.stack : null,
      },
      { status: 500 }
    );
  }
}
