import { useForm, Link } from '@inertiajs/react';
// import { useRoute } from '../../../vendor/tightenco/ziggy';
export default function ExchangeMoneyForm() {

    const { data, setData, post, errors, processing } = useForm({
        Customer_name : "",
        phone : "",
        enter_amount : "",
    });
    // console.log(useForm());
    function submit(e) {
        e.preventDefault();
        post("/calculateMoney");
    }
    console.log(errors);
  return (
    <div>
        <style>{`
  .redborder {
    border: 1px solid red;
  }
`}</style>
      <h1>This is ExchangeMoneyForm page</h1>
      {data.Customer_name}
      <form onSubmit={submit}>
       Customer_name : <input type="text" className={errors.Customer_name && 'redborder'} value={data.Customer_name} onChange={(e) => setData('Customer_name', e.target.value)}/>
       {errors.Customer_name && ( <p style={{ color: 'red' }}>{errors.Customer_name}</p> )}<br/>

        phone :<input type="text" className={errors.phone && 'redborder'} value={data.phone} onChange={(e) => setData('phone', e.target.value)}/>
        {errors.phone && ( <p style={{ color: 'red' }}>{errors.phone}</p> )}<br/>

        Select : pair<select name="country" defaultValue="uk">
            <option value="us">United States</option>
            <option value="ca">Canada</option>
            <option value="uk">United Kingdom</option>
        </select><br/>

        enter_amount :<input type="text" className={errors.enter_amount && 'redborder'} value={data.enter_amount} onChange={(e) => setData('enter_amount', e.target.value)}/>
        {errors.enter_amount && ( <p style={{ color: 'red' }}>{errors.enter_amount}</p> )}<br/>
      
        <button type="submit" disabled={processing}>
            {processing ? 'Calculating...' : 'Calculate'}
        </button>
      </form>


       <Link href={route('invoices.show', 1)} >Show Invoices</Link><br/>
      
    </div>
  );
}