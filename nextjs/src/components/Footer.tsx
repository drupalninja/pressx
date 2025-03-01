import SiteFooter, { SiteFooterProps } from './site-footer/SiteFooter';
import { graphQLClient } from "@/lib/graphql";
import getConfig from 'next/config';

const { publicRuntimeConfig } = getConfig();

type MenuItem = {
  id: string;
  label: string;
  path: string;
};

type FooterMenuResponse = {
  menuItems: {
    nodes: MenuItem[];
  };
};

const FooterMenuQuery = `
  query FooterMenu {
    menuItems(where: { location: FOOTER }) {
      nodes {
        id
        label
        path
      }
    }
  }
`;

async function getFooterMenu() {
  try {
    const data = await graphQLClient.request<FooterMenuResponse>(FooterMenuQuery);
    return data.menuItems;
  } catch (error) {
    console.error('Error fetching footer menu. Full error:', error);
    if (error instanceof Error) {
      console.error('Error message:', error.message);
    }
    return null;
  }
}

export default async function Footer() {
  const menuItems = await getFooterMenu();

  // Convert WordPress menu items to footer links format, similar to Header.tsx
  const links: SiteFooterProps['links'] = menuItems?.nodes?.map((item: MenuItem) => ({
    title: item.label,
    url: item.path,
  })) || [];

  return (
    <SiteFooter
      links={links}
      siteLogo={publicRuntimeConfig?.LOGO_URL || '/images/logo.svg'}
      siteLogoWidth={parseInt(publicRuntimeConfig?.LOGO_WIDTH || '160')}
      siteLogoHeight={parseInt(publicRuntimeConfig?.LOGO_HEIGHT || '42')}
      siteName={publicRuntimeConfig?.SITE_NAME || 'PressX'}
      showLogo={publicRuntimeConfig?.SHOW_LOGO !== '0'}
    />
  );
}
