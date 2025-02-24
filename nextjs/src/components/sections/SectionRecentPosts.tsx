import RecentCards from '@/components/recent-cards/RecentCards';

export interface RecentPostsSection {
  type: 'recent_posts';
  title?: string;
  recentPosts: Array<{
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
  }>;
}

export default function SectionRecentPosts({ section }: { section: RecentPostsSection }) {
  const recentCardResults = section.recentPosts.map(post => ({
    id: post.databaseId.toString(),
    path: `/post/${post.slug}`,
    title: post.title,
    summary: post.excerpt,
    media: post.featuredImage?.node ? (
      <img
        src={post.featuredImage.node.sourceUrl}
        alt={post.featuredImage.node.altText || ''}
        className="w-full h-full object-cover"
      />
    ) : (
      <img
        src="/images/card.webp"
        alt="Placeholder"
        className="w-full h-full object-cover"
      />
    ),
    metadata: {
      date: new Date(post.date).toLocaleDateString(),
      tags: post.tags?.nodes || [],
    },
  }));

  return (
    <div className="container mx-auto my-6 lg:my-25">
      {section.title && (
        <h2 className="text-3xl font-bold mb-8">{section.title}</h2>
      )}
      <RecentCards results={recentCardResults} />
    </div>
  );
}

// GraphQL fragment for recent posts sections
export const recentPostsSectionFragment = `
  fragment RecentPostsSection on LandingSection {
    type
    title
    recentPosts {
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
  }
`;