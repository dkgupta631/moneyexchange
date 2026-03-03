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
        <style>
            {`
                .redborder {
                  border: 1px solid red;
                }
              `}
      </style>
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
      

      
       <div className="container-fluid py-5">
            <div className="container px-lg-5">
                <div className="row justify-content-center">
                    <div className="col-lg-7">
                        <div className="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                            <h6 className="position-relative d-inline text-primary ps-4">Contact Us</h6>
                            <h2 className="mt-2">Contact For Any Query</h2>
                        </div>
                        <div className="wow fadeInUp" data-wow-delay="0.3s">
                            <h4 className="text-center mb-4">Receive messages instantly with our PHP and Ajax contact form - available in the <a href="https://htmlcodex.com/downloading/?item=2059">Pro Version</a> only.</h4>
                            <form>
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <div className="form-floating">
                                            <input type="text" className="form-control" id="name" placeholder="Your Name"/>
                                            <label htmlFor="name">Your Name</label>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="form-floating">
                                            <input type="email" className="form-control" id="email" placeholder="Your Email"/>
                                            <label htmlFor="email">Your Email</label>
                                        </div>
                                    </div>
                                    <div className="col-12">
                                        <div className="form-floating">
                                            <input type="text" className="form-control" id="subject" placeholder="Subject"/>
                                            <label htmlFor="subject">Subject</label>
                                        </div>
                                    </div>
                                    <div className="col-12">
                                        <div className="form-floating">
                                            <textarea className="form-control" placeholder="Leave a message here" id="message" style={{ height: '150px' }}></textarea>
                                            <label htmlFor="message">Message</label>
                                        </div>
                                    </div>
                                    <div className="col-12">
                                        <button className="btn btn-primary w-100 py-3" type="submit">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>
    
  );
}