import { Head, Link } from '@inertiajs/react';

export default function WebLayout({children}) {
  return (
    <>
    <Head>
      <meta head-key="description" name="description" content="This is the default description" />
      <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    </Head>
      <header>
        <nav>
            <Link href="/">Home</Link>
            <Link href="/moneyexchange">moneyexchange</Link>
        </nav>
      </header>

      <main>
        {children}
      </main>
      
    </>
  );
}