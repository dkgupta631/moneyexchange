import { useState } from 'react';
import { usePage } from '@inertiajs/react';
export default function ShowInvoices({records}) {

    console.log(usePage());
    const { flash } = usePage().props;
    const [flashMsg, setflashMsg] = useState(flash.greet);
    setTimeout(() => {
        setflashMsg(null)
    }, 20000);

  return (
    <div>
     {flashMsg && <div style={{ color: 'green' }}>{flashMsg}</div>}
      <h1>This is Show Invoices page</h1>
       <div>
     
     
   
        {records.map(row => (

            <div key={row.id}>
              <p>{row.from_currency} - {row.to_currency}</p>
              <p></p>
            </div>

        ))}
      </div>
      
    </div>
      
   
  );
}