import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import Post from '@/components/Post';
import { PostResponse } from '@/types/wordpress';

const getPostQuery = `
  query GetPost($slug: ID!) {
    post(id: $slug, idType: URI) {
      title
      content
      excerpt
      date
      databaseId
      featuredImage {
        node {
          sourceUrl
          altText
          mediaDetails {
            width
            height
          }
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

export default async function PostPage({
  params: { slug },
}: {
  params: { slug: string };
}) {
  const data = await graphQLClient.request<PostResponse>(getPostQuery, { slug });

  if (!data?.post) {
    notFound();
  }

  return <Post post={data.post} />;
}

export async function generateMetadata({
  params: { slug },
}: {
  params: { slug: string };
}): Promise<Metadata> {
  try {
    const data = await graphQLClient.request<PostResponse>(getPostQuery, { slug });

    return {
      title: data.post?.title || slug,
    };
  } catch (error) {
    return {
      title: slug,
    };
  }
}
