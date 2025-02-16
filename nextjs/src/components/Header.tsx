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
    <header role="banner" className="sticky top-0 z-50 w-full bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 mb-8">
      <div className="lg:container mx-auto py-6">
        <MainMenu
          siteLogo='/images/logo.svg'
          menuItems={transformedMenuItems}
          showSiteName={false}
          showLogo={true}
          siteName="PressX"
          ctaLinkCount={2}
          modifier="p-0"
          siteLogoWidth={200}
          siteLogoHeight={100}
        />
      </div>
    </header>
  );
}
