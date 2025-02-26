import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import { graphQLClient } from '@/lib/graphql';

interface HomepageSettings {
  homepageSettings: {
    showOnFront: string;
    pageOnFront: number;
  };
  landing?: {
    databaseId: number;
    slug: string;
  };
}

const query = `
  query HomepageSettings {
    homepageSettings {
      showOnFront
      pageOnFront
    }
    landing(id: "home", idType: SLUG) {
      databaseId
      slug
    }
  }
`;

export async function middleware(request: NextRequest) {
  // Only run on homepage requests
  if (request.nextUrl.pathname !== '/') {
    return NextResponse.next();
  }

  try {
    const data = await graphQLClient.request<HomepageSettings>(query);
    const { homepageSettings } = data;

    // If showing a static page as homepage
    if (homepageSettings.showOnFront === 'page') {
      // If homepage is set to the landing page with slug "home"
      if (data.landing && data.landing.databaseId === homepageSettings.pageOnFront) {
        return NextResponse.rewrite(new URL('/home', request.url));
      }
      // If homepage is set to a different page
      return NextResponse.redirect(new URL(`/${homepageSettings.pageOnFront}`, request.url));
    }

    // If showing latest posts as homepage
    return NextResponse.rewrite(new URL('/posts', request.url));
  } catch (error) {
    console.error('Error in homepage middleware:', error);
    return NextResponse.next();
  }
}

export const config = {
  matcher: '/',
};
