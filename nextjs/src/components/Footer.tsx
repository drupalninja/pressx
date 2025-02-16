export default function Footer() {
  return (
    <footer className="py-8 mt-8 border-t">
      <div className="text-center">
        <p className="text-sm text-gray-600">
          © {new Date().getFullYear()} PressX. All rights reserved.
        </p>
      </div>
    </footer>
  );
}
