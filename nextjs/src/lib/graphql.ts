import { GraphQLClient } from 'graphql-request';
import { cookies } from 'next/headers';

const endpoint = process.env.NEXT_PUBLIC_WORDPRESS_API_URL || 'https://pressx.ddev.site/graphql';

// Create headers for GraphQL requests
const createHeaders = (forceAuth = false) => {
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
  };

  // Only check for JWT token if preview mode is enabled or auth is forced
  const isPreviewMode = process.env.NEXT_PUBLIC_PREVIEW_MODE === 'true';

  if (isPreviewMode || forceAuth) {
    // Only try to access cookies in a server component context
    if (typeof window === 'undefined') {
      try {
        const cookieStore = cookies();
        const jwtToken = cookieStore.get('wp_jwt_token')?.value;

        if (jwtToken) {
          headers['Authorization'] = `Bearer ${jwtToken}`;
        }
      } catch (error) {
        // Silently handle errors - we're in a context where cookies aren't available
        // This is normal for many rendering scenarios
      }
    }
  }

  return headers;
};

// Create a GraphQL client with the configured headers
// For regular content - no auth needed
export const graphQLClient = new GraphQLClient(endpoint, {
  headers: {
    'Content-Type': 'application/json',
  },
});

// Special client for preview requests with JWT authentication
export const previewClient = new GraphQLClient(endpoint, {
  headers: createHeaders(true),
});

// Function to get a client with a specific JWT token
export const getAuthenticatedClient = (jwtToken: string) => {
  return new GraphQLClient(endpoint, {
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${jwtToken}`,
    },
  });
};

// Function to get JWT token (for client components)
export async function getJwtToken() {
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
              clientMutationId: "nextjs-client",
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
