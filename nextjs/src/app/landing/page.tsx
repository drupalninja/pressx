import { graphQLClient } from '@/lib/graphql';
import Link from 'next/link';

const query = `
  query AllLandingPages {
    landings {
      nodes {
        databaseId
        title
        slug
      }
    }
  }
`;

interface LandingPagesData {
  landings: {
    nodes: Array<{
      databaseId: number;
      title: string;
      slug: string;
    }>;
  };
}

export default async function LandingPages() {
  try {
    const data = await graphQLClient.request<LandingPagesData>(query);
    console.log('Available Landing Pages:', data);

    return (
      <main className="min-h-screen p-8">
        <h1 className="text-3xl font-bold mb-6">Available Landing Pages</h1>
        <div className="space-y-4">
          {data.landings.nodes.map((page) => (
            <div key={page.databaseId} className="p-4 border rounded">
              <h2 className="text-xl font-semibold">{page.title}</h2>
              <div className="mt-2 space-x-4">
                <Link
                  href={`/landing/${page.databaseId}`}
                  className="text-blue-600 hover:underline"
                >
                  View by ID
                </Link>
                <Link
                  href={`/landing/${page.slug}`}
                  className="text-blue-600 hover:underline"
                >
                  View by Slug
                </Link>
              </div>
              <div className="mt-2 text-sm text-gray-600">
                <p>Database ID: {page.databaseId}</p>
                <p>Slug: {page.slug}</p>
              </div>
            </div>
          ))}
        </div>
      </main>
    );
  } catch (error) {
    console.error('Error fetching landing pages:', error);
    return (
      <main className="min-h-screen p-8">
        <h1 className="text-3xl font-bold mb-6">Error Loading Landing Pages</h1>
        <pre className="bg-red-50 p-4 rounded text-red-600">
          {JSON.stringify(error, null, 2)}
        </pre>
      </main>
    );
  }
}
