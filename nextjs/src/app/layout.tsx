import { Open_Sans } from "next/font/google";
import Container from "@/components/Container";
import Footer from "@/components/Footer";
import Header from "@/components/Header";

import './globals.css'

const font = Open_Sans({ subsets: ["latin"] });

export const metadata = {
  title: 'PressX',
  description: 'Next.js + WordPress Headless CMS',
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body className={font.className}>
        <Container>
          <Header />
          {children}
          <Footer />
        </Container>
      </body>
    </html>
  );
}
