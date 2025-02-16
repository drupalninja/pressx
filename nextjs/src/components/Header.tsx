import MainMenu from './main-menu/MainMenu';
import { MainMenuItem } from './main-menu/Types';

interface MenuItem {
  id: string;
  title: string;
  url: string;
  children?: MenuItem[];
}

interface HeaderProps {
  mainMenu: MenuItem[];
}

export default function Header({ mainMenu }: HeaderProps) {
  // Transform WordPress menu items to MainMenu format
  const transformedMenuItems: MainMenuItem[] = mainMenu.map(item => ({
    title: item.title,
    url: item.url,
    below: item.children?.map(child => ({
      title: child.title,
      url: child.url,
    })),
  }));

  console.log(transformedMenuItems);

  return (
    <header>
      <MainMenu
        menuItems={transformedMenuItems}
        showSiteName={true}
        siteName="PressX"
        ctaLinkCount={1} // The last menu item will be styled as a CTA
        modifier="bg-background border-b"
        linkModifier="hover:text-primary transition-colors"
        siteLogoWidth={200}
        siteLogoHeight={100}
      />
    </header>
  );
}
