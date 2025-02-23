import { graphQLClient } from '@/lib/graphql';
import { getImage } from '@/components/helpers/Utilities';
import RecentCards from '@/components/recent-cards/RecentCards';
import Pager from '@/components/pager/Pager';
import Image from 'next/image';

interface Post {
  databaseId: number;
  title: string;
  slug: string;
  excerpt: string;
  date: string;
  featuredImage?: {
    node: {
      sourceUrl: string;
      altText?: string;
    };
  };
  tags?: {
    nodes: Array<{
      name: string;
      slug: string;
    }>;
  };
}

interface BlogPageData {
  posts: {
    nodes: Post[];
    pageInfo: {
      endCursor: string;
      startCursor: string;
      hasNextPage: boolean;
      hasPreviousPage: boolean;
    };
  };
}

interface PostCountData {
  posts: {
    nodes: Array<{ databaseId: number }>;
  };
}

const POSTS_PER_PAGE = 6;

const getBlogPostsQuery = `
  query GetBlogPosts($first: Int!, $after: String) {
    posts(first: $first, after: $after) {
      nodes {
        databaseId
        title
        slug
        excerpt
        date
        featuredImage {
          node {
            sourceUrl
            altText
          }
        }
        tags {
          nodes {
            name
            slug
          }
        }
      }
      pageInfo {
        endCursor
        startCursor
        hasNextPage
        hasPreviousPage
      }
    }
  }
`;

const getPostCountQuery = `
  query GetPostCount {
    posts(first: 10000) {
      nodes {
        databaseId
      }
    }
  }
`;

export default async function PostsPage({
  searchParams,
}: {
  searchParams: { page?: string };
}) {
  const currentPage = Number(searchParams.page) || 1;
  const after = currentPage > 1 ? btoa(`arrayconnection:${(currentPage - 1) * POSTS_PER_PAGE - 1}`) : undefined;

  // Fetch posts for current page
  const data = await graphQLClient.request<BlogPageData>(getBlogPostsQuery, {
    first: POSTS_PER_PAGE,
    after,
  });

  // Fetch total count
  const countData = await graphQLClient.request<PostCountData>(getPostCountQuery);
  const totalPosts = countData.posts.nodes.length;
  const totalPages = Math.ceil(totalPosts / POSTS_PER_PAGE);

  const posts = data.posts.nodes;
  const { hasNextPage, hasPreviousPage } = data.posts.pageInfo;

  // Transform posts into the format expected by RecentCards
  const recentCardResults = posts.map(post => {
    let featuredImage;
    if (post.featuredImage?.node) {
      featuredImage = getImage(
        {
          sourceUrl: post.featuredImage.node.sourceUrl,
          altText: post.featuredImage.node.altText,
        },
        'w-full h-full object-cover',
        'i43medium'
      );
    } else {
      // Use placeholder image
      featuredImage = (
        <Image
          src="/images/card.webp"
          alt="Placeholder"
          width={800}
          height={600}
          className="w-full h-full object-cover"
        />
      );
    }

    return {
      id: post.databaseId.toString(),
      path: `/post/${post.slug}`,
      title: post.title,
      summary: post.excerpt,
      media: featuredImage,
      metadata: {
        date: new Date(post.date).toLocaleDateString(),
        tags: post.tags?.nodes || [],
      },
    };
  });

  // Generate pagination items with all page numbers
  const pagerItems = {
    previous: hasPreviousPage
      ? { href: `/post?page=${currentPage - 1}`, text: 'Previous' }
      : undefined,
    pages: Array.from({ length: totalPages }, (_, i) => ({
      href: `/post?page=${i + 1}`,
      current: i + 1 === currentPage,
    })),
    next: hasNextPage
      ? { href: `/post?page=${currentPage + 1}`, text: 'Next' }
      : undefined,
  };

  return (
    <main className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8">Posts</h1>
      <RecentCards results={recentCardResults} />
      <div className="container mt-12">
        <Pager headingId="posts-pagination" pagerItems={pagerItems} />
      </div>
    </main>
  );
}
