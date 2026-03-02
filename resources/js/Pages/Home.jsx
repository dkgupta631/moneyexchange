import { Head, Link } from '@inertiajs/react';

 export default function Home({records}) {
  // console.log(records);
  return (
    <>
    {/* <Head title="Home" /> */}
    <Head>
      <title>Home</title>
      <meta head-key="description" name="description" content="This is a page specific description" />
  </Head>
      
      <h1>Today Exchange Rate</h1>
      <Link href={route('open.moneyexchange.form')} >Exchange</Link>
       <br/>
      <p>Time : {new Date().toLocaleTimeString()}</p>
      <div>
        {records.map(row => (

            <div key={row.id}>
              <p>{row.from_currency} - {row.to_currency}</p>
              <p></p>
            </div>

        ))}
      </div>
      
    </>
  );
}

