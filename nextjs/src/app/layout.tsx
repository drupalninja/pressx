import { Open_Sans } from "next/font/google";
import Container from "@/components/Container";
import Footer from "@/components/Footer";
import Header from "@/components/Header";
import { graphQLClient } from "@/lib/graphql";
import './globals.css'
import AdminBarWrapper from "@/components/admin/AdminBarWrapper";

const font = Open_Sans({ subsets: ["latin"] });

export const metadata = {
  title: 'PressX',
  description: 'Next.js + WordPress Headless CMS',
}

interface MenuItem {
  id: string;
  title: string;
  url: string;
  children?: MenuItem[];
}

interface MenuResponse {
  menuItems: {
    nodes: Array<{
      id: string;
      label: string;
      path: string;
      childItems: {
        nodes: Array<{
          id: string;
          label: string;
          path: string;
        }>;
      };
    }>;
  };
}

async function getMainMenu(): Promise<MenuItem[]> {
  const query = `
    query GetMainMenu {
      menuItems(where: { location: PRIMARY }) {
        nodes {
          id
          label
          path
          childItems {
            nodes {
              id
              label
              path
            }
          }
        }
      }
    }
  `;

  try {
    const data = await graphQLClient.request<MenuResponse>(query);
    const menuItems = data.menuItems.nodes;

    return menuItems.map(item => ({
      id: item.id,
      title: item.label,
      url: item.path,
      children: item.childItems.nodes.map(child => ({
        id: child.id,
        title: child.label,
        url: child.path,
      })),
    }));
  } catch (error) {
    console.error('Error fetching menu:', error);
    return [];
  }
}

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const mainMenu = await getMainMenu();
  const isPreviewMode = process.env.NEXT_PUBLIC_PREVIEW_MODE === 'true';

  return (
    <html lang="en">
      <body className={`${font.className} ${isPreviewMode ? 'pt-10' : ''}`}>
        <AdminBarWrapper isPreviewMode={isPreviewMode} />
        <Container>
          <Header mainMenu={mainMenu} />
          {children}
          <Footer />
        </Container>
      </body>
    </html>
  );
}
