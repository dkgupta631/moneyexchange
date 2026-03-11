import { useForm, Head, usePage, Link } from '@inertiajs/react';
import { useEffect, useState } from "react";
import Select from "react-select";
import axios from "axios";
export default function ExchangeMoneyForm() {

        const { translations, locale, ziggy } = usePage().props;
        const appUrl = window.location.origin;
        const t = (key) => translations[key] ?? key;

        const currencies = [
            { value: "Dollar", label: "US Dollar", flag: "/website/assets/flags/USD.png" },
            { value: "Baht", label: "Thai Baht", flag: "/website/assets/flags/THB.png" },
            { value: "Riel", label: "Khmer Riel", flag: "/website/assets/flags/KHR.png" },
            ];
        const formatOptionLabel = ({ value, label, flag }) => (
            <div style={{ display: "flex", alignItems: "center", width: "100%" }}>
                <img src={flag} style={{ width: 22, height: 16, marginRight: 10 }} />
                <span>{value}</span>

            <span style={{ marginLeft: "auto", color: "#666", display: "flex", alignItems: "center", gap: "8px" }}>
                    {label}
                    <i className="fa fa-angle-right"></i>
                </span>
            </div>
        );

        const { data, setData, post, errors, processing } = useForm({
        customer_name : "",
        phone : "",
        from_currency : "Dollar",
        to_currency : "Baht",
        enter_amount : "",
        exchange_type : "Normal",
        where_to_send : "TRF-IN",
    });
    // console.log(useForm());
    function submit(e) {
        e.preventDefault();
        post("/calculateMoney");
    }
    // console.log(errors);




    const [exchangeRate,setExchangeRate] = useState(0);
    const [receivedAmount,setReceivedAmount] = useState(0);
    const [serviceFee,setServiceFee] = useState(0);
    const [loadingRate,setLoadingRate] = useState(false);

    const fetchRate = async (from_currency,to_currency,exchange_type,enter_amount) => {
        setLoadingRate(true);
        const res = await axios.post("/get-exchange-rate",{
            from_currency,
            to_currency,
            exchange_type,
            enter_amount

        });
        console.log(res.data);
        // console.log("FROM:", data.from_currency);
        // console.log("TO:", data.to_currency);
        // console.log("TYPE:", data.exchange_type);
        setExchangeRate(res.data.exchange_rate);

        setReceivedAmount(res.data.total.toFixed(2));
        setServiceFee(res.data.service_fee);

        setLoadingRate(false);
    };


    
   useEffect(() => {
        if (!data.enter_amount || data.enter_amount <= 0) return;
        fetchRate(
            data.from_currency,
            data.to_currency,
            data.exchange_type,
            data.enter_amount
        );

    }, [
        data.from_currency,
        data.to_currency,
        data.exchange_type,
        data.enter_amount
    ]);

//     useEffect(()=>{
//     if(!data.enter_amount || !exchangeRate) return;
//     let subtotal = data.enter_amount / exchangeRate;
//     let fee = subtotal * 0.02;
//     let final = subtotal;

//     setReceivedAmount(final.toFixed(2));
//     setServiceFee(fee.toFixed(2));

// },[data.enter_amount, exchangeRate]);




    
  return (
    <>
        <Head title={t('International')} />
       <div className="container-fluid py-5"><br/><br/><br/>
            <div className="container px-lg-5">
                <div className="row justify-content-center">
                    <div className="col-lg-7">
                        <div className="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                            <h6 className="position-relative d-inline text-primary ps-4">{t('Exchange your money with confidence. Our rates are updated daily based on the international market to ensure fair and transparent transactions')}</h6>
                            {/* <h2 className="mt-2">Contact For Any Query</h2> */}
                        </div>
                        <div className="wow fadeInUp" data-wow-delay="0.3s">
                            <h4 className="text-center mb-4">{t('Live Exchange Rates – USD | THB | KHR')}</h4>
                            <form onSubmit={submit}>
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <div className="form-floating">
                                            <input type="text"  className={`form-control ${errors.customer_name ? 'redborder' : ''}`} value={data.customer_name} onChange={(e) => setData('customer_name', e.target.value)} placeholder={t('Enter Name')}/>
                                                {errors.customer_name && ( <p style={{ color: 'red' }}>{errors.customer_name}</p> )}
                                            <label htmlFor="name">{t('Customer name')} ({t('optional')})</label>
                                            
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="form-floating">
                                            <input type="text" className={`form-control ${errors.phone ? 'redborder' : ''}`} value={data.phone} onChange={(e) => setData('phone', e.target.value)} placeholder={t('Enter phone number')}/>
                                                {errors.phone && ( <p style={{ color: 'red' }}>{errors.phone}</p> )}
                                            <label htmlFor="phone">{t('Phone Number')} ({t('optional')})</label>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <label className="mb-2">{t('From currency')}</label>
                                        <Select
                                            options={currencies}
                                            value={currencies.find(c => c.value === data.from_currency)}
                                            formatOptionLabel={formatOptionLabel}
                                            className="form-floating currency-select"
                                            onChange={(selected) => {
                                                    setData("from_currency", selected.value);
                                                    fetchRate(
                                                        selected.value,
                                                        data.to_currency,
                                                        data.exchange_type
                                                    );
                                                }}
                                        />
                                    </div>
                                    <div className="col-md-6">
                                        <label className="mb-2">{t('To currency')}</label>
                                        <Select
                                            options={currencies}
                                            value={currencies.find(c => c.value === data.to_currency)}
                                            // onChange={(selected) => setData("to_currency", selected.value)}
                                            formatOptionLabel={formatOptionLabel}
                                            className="form-floating currency-select"
                                            onChange={(selected) => {
                                                setData("to_currency", selected.value);

                                                fetchRate(
                                                    data.from_currency,
                                                    selected.value,
                                                    data.exchange_type
                                                );
                                            }}
                                        />
                                    </div>
                                    <div className="col-12">
                                        <div className="form-floating">
                                              <input type="number" className={`form-control ${errors.enter_amount ? 'redborder' : ''}`} value={data.enter_amount} onChange={(e)=> setData('enter_amount', e.target.value)} placeholder={t('Enter amount')} />
                                                {errors.enter_amount && ( <p style={{ color: 'red' }}>{errors.enter_amount}</p> )}
                                            <label htmlFor="enter_amount">{t('Amount')}</label>
                                        </div>
                                    </div>
                                    {Number(data.enter_amount) > 0 && (
                                        <>
                                            <span className="text-primary text-center animated zoomIn"><b>{t('Real time update')}</b></span>
                                            <div className="card p-3">
                                                <h6>{t('Exchange Summary')}</h6>
                                               <p><b>{data.from_currency}  ⇄  {data.to_currency}   ⟹   {loadingRate ? ( <span className="text-success">{t('Loading')}...</span> ) : ( exchangeRate )}</b></p>
                                                <p>{t('Amount')} : {data.enter_amount} {data.from_currency}</p>
                                                <p>{t('Subtotal')} : {receivedAmount} {data.to_currency}</p>
                                                <p>{t('Service Fee')} : {serviceFee}</p>
                                                <h5>{t('Total Receive amount')} : {receivedAmount} {data.to_currency}</h5>
                                            </div>
                                        </>
                                    )}

                                    <div className="col-md-6">
                                        <div className="form-floating">
                                                <select className="form-control" value={data.exchange_type} 
                                                // onChange={(e) => setData('exchange_type', e.target.value)}
                                                onChange={(e) => {
                                                    setData("exchange_type", e.target.value);

                                                    fetchRate(
                                                        data.from_currency,
                                                        data.to_currency,
                                                        e.target.value
                                                    );
                                                }}
                                                >
                                                    <option value="Normal">{t('Normal')}</option>
                                                    <option value="Standard">{t('Standard')}</option>
                                                </select>   
                                            <label htmlFor="exchange_type">{t('Exchange Type')}</label>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="form-floating">
                                                <select className="form-control" value={data.where_to_send} onChange={(e) => setData('where_to_send', e.target.value)}>
                                                    <option value="TRF-OUT">{t('TRF-OUT')}</option>
                                                    <option value="TRF-IN">{t('TRF-IN')}</option>
                                                    <option value="Bank to Cash">{t('Bank to Cash')}</option>
                                                    <option value="Cash to Bank">{t('Cash to Bank')}</option>
                                                </select>   
                                            <label htmlFor="where_to_send">{t('Where to send')}</label>
                                        </div>
                                    </div>
                                    <div className="col-12">
                                        <button className="btn btn-primary w-100 py-3" type="submit" disabled={processing}>{processing ? t('Generating...') : t('Generate invoice')} ⟶</button>
                                    </div>
                                    <p className="text-center mb-4">{t('Secure and fast currency exchange with the best daily rates')}.</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <br/>

       

    </>
    
  );
}