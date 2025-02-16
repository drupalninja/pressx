import Link from 'next/link';

export default function NotFound() {
  return (
    <div className="min-h-screen flex items-center justify-center">
      <div className="text-center">
        <h2 className="text-2xl font-bold mb-4">Landing Page Not Found</h2>
        <p className="mb-4">Could not find the requested landing page.</p>
        <Link
          href="/"
          className="text-blue-600 hover:text-blue-800 underline"
        >
          Return Home
        </Link>
      </div>
    </div>
  );
}
