import Link from 'next/link';

export default function Header() {
  return (
    <header className="py-6">
      <nav className="flex items-center justify-between">
        <div className="flex items-center">
          <Link href="/" className="text-2xl font-bold">
            PressX
          </Link>
        </div>
      </nav>
    </header>
  );
}
