import { useForm, Head, usePage } from '@inertiajs/react';
import { useEffect, useState } from "react";
import axios from "axios";

export default function ExchangeMoneyForm() {

    const { translations } = usePage().props;
    const t = (key) => translations[key] ?? key;

    const currencies = [
        { value: "Dollar", label: "US Dollar", code: "USD", flag: "/website/assets/flags/USD.png" },
        { value: "Baht",   label: "Thai Baht",  code: "THB", flag: "/website/assets/flags/THB.png" },
        { value: "Riel",   label: "Khmer Riel", code: "KHR", flag: "/website/assets/flags/KHR.png" },
    ];

    const getCurrencySymbol = (val) => ({ Dollar: "$", Baht: "฿", Riel: "៛" }[val] || "");
    const getCurrencyByValue = (val) => currencies.find(c => c.value === val);

    const { data, setData, post, errors, processing } = useForm({
        customer_name:    "",
        phone:            "",
        from_currency:    "Dollar",
        to_currency:      "Baht",
        enter_amount:     "",
        exchange_type:    "Normal",
        where_to_send:    "TRF-IN",
        receive_type:     "Cash to Cash",
        // invoice fields — filled automatically when rate is fetched
        exchange_rate_id: "",
        exchange_rate:    "",
        subtotal:         "",
        service_fee:      "",
        final_amount:     "",
    });

    const [receivedAmount,   setReceivedAmount]   = useState("");
    const [exchangeRate,     setExchangeRate]     = useState(""); // raw DB rate (stored in invoice)
    const [displayRate,      setDisplayRate]      = useState(""); // correct display: 1 FROM = X TO
    const [serviceFee,       setServiceFee]       = useState("");
    const [loadingRate,      setLoadingRate]      = useState(false);
    const [fromOpen,         setFromOpen]         = useState(false);
    const [toOpen,           setToOpen]           = useState(false);

    const fetchRate = async (from_currency, to_currency, exchange_type, enter_amount) => {
        if (!enter_amount || Number(enter_amount) <= 0) return;
        setLoadingRate(true);
        try {
            const res      = await axios.post("/get-exchange-rate", { from_currency, to_currency, exchange_type, enter_amount });
            const rawRate  = res.data.exchange_rate;          // DB rate (e.g. 31.14)
            const buyOrSell= res.data.buy_or_sell;            // 'sell' = multiply, else divide
            const total    = parseFloat(res.data.total).toFixed(2);
            const fee      = res.data.service_fee;
            const rid      = res.data.exchange_rate_id ?? "";

            // ── Compute correct display rate: how much 1 unit of FROM currency gives in TO ──
            // If buy_or_sell = 'sell'  → total = amount * rate  → 1 FROM = rate TO        ✓
            // If buy_or_sell = 'buy'   → total = amount / rate  → 1 FROM = (1/rate) TO
            const oneUnitResult = buyOrSell === 'sell'
                ? parseFloat(rawRate)
                : parseFloat((1 / rawRate).toFixed(6));

            setExchangeRate(rawRate);                         // keep raw for invoice storage
            setDisplayRate(oneUnitResult);                    // shown in UI
            setReceivedAmount(total);
            setServiceFee(fee);

            setData(prev => ({
                ...prev,
                exchange_rate:    rawRate,
                exchange_rate_id: rid,
                subtotal:         total,
                service_fee:      fee,
                final_amount:     total,
            }));
        } catch (e) {
            console.error(e);
        }
        setLoadingRate(false);
    };

    useEffect(() => {
        fetchRate(data.from_currency, data.to_currency, data.exchange_type, data.enter_amount);
    }, [data.from_currency, data.to_currency, data.exchange_type, data.enter_amount]);

    const handleSwap = () => {
        setData(prev => ({ ...prev, from_currency: prev.to_currency, to_currency: prev.from_currency }));
    };

    const closeAll = () => { setFromOpen(false); setToOpen(false); };

    function submit(e) {
        e.preventDefault();
        post("/calculateMoney");
    }

    const fromCurrency = getCurrencyByValue(data.from_currency);
    const toCurrency   = getCurrencyByValue(data.to_currency);
    const hasResult    = Number(data.enter_amount) > 0 && receivedAmount;

    return (
        <>
            <Head title={t('International')} />

            <style>{`
                .xform { font-family: 'Segoe UI', system-ui, sans-serif; }

                /* ── Exchanger Card ── */
                .xcard {
                    display: grid;
                    grid-template-columns: 1fr 52px 1fr;
                    border: 1.5px solid #d4b3e8;
                    border-radius: 14px;
                    background: #fff;
                    box-shadow: 0 2px 16px rgba(91,45,142,.07);
                    position: relative;
                    overflow: visible;
                }
                .xside {
                    padding: 14px 18px 16px;
                    display: flex;
                    flex-direction: column;
                    min-width: 0;
                    position: relative;
                }
                .xside-from { border-right: 1.5px solid #d4b3e8; }

                .xside-label {
                    font-size: 11px; font-weight: 700;
                    letter-spacing: .7px; text-transform: uppercase;
                    color: #9aa8be; margin-bottom: 6px;
                }

                /* Amount */
                .xamount-row { display: flex; align-items: baseline; gap: 4px; }
                .xsymbol     { font-size: 24px; font-weight: 700; color: #1a1f36; line-height: 1; }
                .xsymbol-to  { color: #5B2D8E; }
                .xamount-input {
                    border: none; outline: none;
                    font-size: 24px; font-weight: 700; color: #1a1f36;
                    width: 100%; background: transparent; min-width: 0;
                }
                .xamount-input::placeholder { color: #d0d8e8; }
                .xamount-input::-webkit-inner-spin-button,
                .xamount-input::-webkit-outer-spin-button { -webkit-appearance: none; }
                .xconverted {
                    font-size: 24px; font-weight: 700; color: #5B2D8E;
                    min-height: 34px; display: flex; align-items: baseline; gap: 4px;
                }
                .xloading { color: #b0bdd0; font-size: 16px; letter-spacing: 3px; align-self: center; }

                /* Currency trigger */
                .xcur-trigger {
                    display: inline-flex; align-items: center; gap: 7px;
                    margin-top: 10px; cursor: pointer; user-select: none; width: fit-content;
                }
                .xcur-trigger img { width: 24px; height: 16px; border-radius: 2px; object-fit: cover; flex-shrink: 0; }
                .xcur-code { font-size: 14px; font-weight: 700; color: #1a1f36; }
                .xcur-name { font-size: 13px; color: #888; font-weight: 400; }
                .xcur-chevron { color: #aaa; font-size: 10px; }

                /* Swap column — sits between the two sides */
                .xswap-col {
                    display: flex; align-items: center; justify-content: center;
                    border-right: 1.5px solid #d4b3e8;
                }
                .xswap-btn {
                    width: 36px; height: 36px; border-radius: 50%;
                    border: 1.5px solid #d4b3e8; background: #fff;
                    display: flex; align-items: center; justify-content: center;
                    cursor: pointer; font-size: 17px; color: #5B2D8E;
                    transition: all .18s; flex-shrink: 0; line-height: 1;
                }
                .xswap-btn:hover { background: #5B2D8E; color: #fff; border-color: #5B2D8E; }

                /* Dropdown */
                .xdropdown {
                    position: absolute; top: calc(100% + 6px); left: 0;
                    background: #fff; border: 1.5px solid #d4b3e8;
                    border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.11);
                    z-index: 200; min-width: 210px; overflow: hidden;
                }
                .xdropdown-right { left: auto; right: 0; }
                .xdrop-item {
                    display: flex; align-items: center; gap: 10px;
                    padding: 10px 14px; cursor: pointer; font-size: 14px;
                    transition: background .12s;
                }
                .xdrop-item:hover { background: #f0e8f8; }
                .xdrop-item img { width: 24px; height: 16px; border-radius: 2px; object-fit: cover; }
                .xdrop-code { font-weight: 700; color: #1a1f36; }
                .xdrop-name { color: #999; font-size: 12px; margin-left: auto; }

                /* Rate line */
                .xrate-line {
                    display: flex; align-items: center; flex-wrap: wrap;
                    gap: 8px; margin-top: 8px; padding: 0 2px;
                    font-size: 13px; color: #444;
                }
                .xrate-line b { color: #1a1f36; }
                .xrate-badge {
                    font-size: 11px; background: #f0e8f8; color: #5B2D8E;
                    padding: 2px 9px; border-radius: 20px; font-weight: 600;
                }
                .xtrack-btn {
                    margin-left: auto;
                    font-size: 12px; font-weight: 600; color: #5B2D8E;
                    background: #f0e8f8; border: 1.5px solid #d4b3e8;
                    border-radius: 8px; padding: 4px 14px; cursor: pointer;
                    text-decoration: none; white-space: nowrap; transition: all .15s;
                }
                .xtrack-btn:hover { background: #5B2D8E; color: #fff; border-color: #5B2D8E; }

                /* Summary */
                .xsummary {
                    background: linear-gradient(135deg,#f5f0fa,#ede0f5);
                    border: 1.5px solid #d4b3e8; border-radius: 12px;
                    padding: 16px 20px;
                }
                .xsummary h6 { font-weight: 700; color: #1a1f36; margin-bottom: 12px; font-size: 15px; }
                .xsum-row {
                    display: flex; justify-content: space-between;
                    font-size: 14px; color: #555; margin-bottom: 5px;
                }
                .xsum-total {
                    font-weight: 700; font-size: 15px; color: #1a1f36;
                    border-top: 1px solid #d4b3e8; padding-top: 9px; margin-top: 5px;
                }
                .xerr { color: #e53e3e; font-size: 12px; margin-top: 3px; }

                @media (max-width: 540px) {
                    .xcard { grid-template-columns: 1fr; }
                    .xside-from { border-right: none; border-bottom: 1.5px solid #d4b3e8; }
                    .xswap-col { border-right: none; border-bottom: 1.5px solid #d4b3e8; padding: 10px 18px; justify-content: flex-start; }
                    .xtrack-btn { margin-left: 0; }
                }
            `}</style>

            <div className="container-fluid py-5 xform" onClick={closeAll}>
                <br /><br /><br />
                <div className="container px-lg-5">
                    <div className="row justify-content-center">
                        <div className="col-lg-7">

                            <div className="section-title text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                                <h6 className="d-inline text-primary ps-4">
                                    {t('Exchange your money with confidence. Our rates are updated daily based on the international market to ensure fair and transparent transactions')}
                                </h6>
                            </div>

                            <div className="wow fadeInUp" data-wow-delay="0.3s">
                                <h4 className="text-center mb-4">{t('Live Exchange Rates – USD | THB | KHR')}</h4>

                                <form onSubmit={submit}>
                                    <div className="row g-3">

                                        {/* Customer name */}
                                        <div className="col-md-6">
                                            <div className="form-floating">
                                                <input type="text"
                                                    className={`form-control ${errors.customer_name ? 'is-invalid' : ''}`}
                                                    value={data.customer_name}
                                                    onChange={e => setData('customer_name', e.target.value)}
                                                    placeholder=" " />
                                                <label>{t('Customer name')} ({t('optional')})</label>
                                                {errors.customer_name && <div className="xerr">{errors.customer_name}</div>}
                                            </div>
                                        </div>

                                        {/* Phone */}
                                        <div className="col-md-6">
                                            <div className="form-floating">
                                                <input type="text"
                                                    className={`form-control ${errors.phone ? 'is-invalid' : ''}`}
                                                    value={data.phone}
                                                    onChange={e => setData('phone', e.target.value)}
                                                    placeholder=" " />
                                                <label>{t('Phone Number')} ({t('optional')})</label>
                                                {errors.phone && <div className="xerr">{errors.phone}</div>}
                                            </div>
                                        </div>

                                        {/* ── Unified Exchanger Card ── */}
                                        <div className="col-12">
                                            <div className="xcard" onClick={e => e.stopPropagation()}>

                                                {/* FROM side */}
                                                <div className="xside xside-from">
                                                    <div className="xside-label">{t('From')}</div>

                                                    <div className="xamount-row">
                                                        <span className="xsymbol">{getCurrencySymbol(data.from_currency)}</span>
                                                        <input
                                                            type="number"
                                                            className="xamount-input"
                                                            placeholder="1"
                                                            value={data.enter_amount}
                                                            onChange={e => setData('enter_amount', e.target.value)}
                                                        />
                                                    </div>
                                                    {errors.enter_amount && <div className="xerr">{errors.enter_amount}</div>}

                                                    <div className="xcur-trigger"
                                                        onClick={() => { setFromOpen(v => !v); setToOpen(false); }}>
                                                        <img src={fromCurrency?.flag} alt={fromCurrency?.code} />
                                                        <span className="xcur-code">{fromCurrency?.code}</span>
                                                        <span className="xcur-name">- {fromCurrency?.label}</span>
                                                        <span className="xcur-chevron">▼</span>
                                                    </div>

                                                    {fromOpen && (
                                                        <div className="xdropdown">
                                                            {currencies.map(c => (
                                                                <div key={c.value} className="xdrop-item"
                                                                    onClick={() => {
                                                                        setData("from_currency", c.value);
                                                                        setFromOpen(false);
                                                                        fetchRate(c.value, data.to_currency, data.exchange_type, data.enter_amount);
                                                                    }}>
                                                                    <img src={c.flag} alt={c.code} />
                                                                    <span className="xdrop-code">{c.code}</span>
                                                                    <span className="xdrop-name">{c.label}</span>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    )}
                                                </div>

                                                {/* SWAP column — centered between both sides */}
                                                <div className="xswap-col">
                                                    <button type="button" className="xswap-btn" onClick={handleSwap} title="Swap">
                                                        ⇄
                                                    </button>
                                                </div>

                                                {/* TO side */}
                                                <div className="xside">
                                                    <div className="xside-label">{t('To')}</div>

                                                    <div className="xconverted">
                                                        <span className="xsymbol xsymbol-to">{getCurrencySymbol(data.to_currency)}</span>
                                                        {loadingRate
                                                            ? <span className="xloading">···</span>
                                                            : <span>{receivedAmount || <span style={{ color: '#d0d8e8', fontWeight: 400, fontSize: 18 }}>0.00</span>}</span>
                                                        }
                                                    </div>

                                                    <div className="xcur-trigger"
                                                        onClick={() => { setToOpen(v => !v); setFromOpen(false); }}>
                                                        <img src={toCurrency?.flag} alt={toCurrency?.code} />
                                                        <span className="xcur-code">{toCurrency?.code}</span>
                                                        <span className="xcur-name">- {toCurrency?.label}</span>
                                                        <span className="xcur-chevron">▼</span>
                                                    </div>

                                                    {toOpen && (
                                                        <div className="xdropdown xdropdown-right">
                                                            {currencies.map(c => (
                                                                <div key={c.value} className="xdrop-item"
                                                                    onClick={() => {
                                                                        setData("to_currency", c.value);
                                                                        setToOpen(false);
                                                                        fetchRate(data.from_currency, c.value, data.exchange_type, data.enter_amount);
                                                                    }}>
                                                                    <img src={c.flag} alt={c.code} />
                                                                    <span className="xdrop-code">{c.code}</span>
                                                                    <span className="xdrop-name">{c.label}</span>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    )}
                                                </div>
                                            </div>

                                            {/* Rate line + Track Exchange Rates button */}
                                            {displayRate && (
                                                <div className="xrate-line">
                                                    <b>1.00 {fromCurrency?.code} = {displayRate} {toCurrency?.code}</b>
                                                    <span className="xrate-badge">{t('Real time update')}</span>
                                                    <a href={route('showExchangeRate')} target="_blank" className="xtrack-btn">{t('Track exchange rates')}</a>
                                                </div>
                                            )}
                                        </div>

                                        {/* Exchange Summary */}
                                        {hasResult && (
                                            <div className="col-12">
                                                <div className="xsummary wow zoomIn">
                                                    <h6>{t('Exchange Summary')}</h6>
                                                    <div className="xsum-row">
                                                        <span>{t('Amount')}</span>
                                                        <span>{getCurrencySymbol(data.from_currency)}{data.enter_amount} {fromCurrency?.code}</span>
                                                    </div>
                                                    <div className="xsum-row">
                                                        <span>{t('Exchange Rate')}</span>
                                                        <span>1 {fromCurrency?.code} = {displayRate} {toCurrency?.code}</span>
                                                    </div>
                                                    <div className="xsum-row">
                                                        <span>{t('Service Fee')}</span>
                                                        <span>{serviceFee}</span>
                                                    </div>
                                                    <div className="xsum-row xsum-total">
                                                        <span>{t('Total Receive amount')}</span>
                                                        <span>{getCurrencySymbol(data.to_currency)}{receivedAmount} {toCurrency?.code}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        )}

                                        {/* Exchange Type | Where to send | Receive Type */}
                                        <div className="col-md-4">
                                            <div className="form-floating">
                                                <select className="form-control" value={data.exchange_type}
                                                    onChange={e => {
                                                        setData("exchange_type", e.target.value);
                                                        fetchRate(data.from_currency, data.to_currency, e.target.value, data.enter_amount);
                                                    }}>
                                                    <option value="Normal">{t('Normal')}</option>
                                                    <option value="Standard">{t('Standard')}</option>
                                                </select>
                                                <label>{t('Exchange Type')}</label>
                                            </div>
                                        </div>

                                        <div className="col-md-4">
                                            <div className="form-floating">
                                                <select className="form-control" value={data.where_to_send}
                                                    onChange={e => setData('where_to_send', e.target.value)}>
                                                    <option value="TRF-OUT">{t('TRF-OUT')}</option>
                                                    <option value="TRF-IN">{t('TRF-IN')}</option>
                                                </select>
                                                <label>{t('Where to send')}</label>
                                            </div>
                                        </div>

                                        <div className="col-md-4">
                                            <div className="form-floating">
                                                <select className="form-control" value={data.receive_type}
                                                    onChange={e => setData('receive_type', e.target.value)}>
                                                    <option value="Cash to Cash">{t('Cash to Cash')}</option>
                                                    <option value="Cash to Bank">{t('Cash to Bank')}</option>
                                                    <option value="Bank to Cash">{t('Bank to Cash')}</option>
                                                    <option value="Bank to Bank">{t('Bank to Bank')}</option>
                                                </select>
                                                <label>{t('Receive Type')}</label>
                                            </div>
                                        </div>

                                        {/* Submit — sends ALL invoice fields via Inertia useForm */}
                                        <div className="col-12">
                                            <button
                                                className="btn btn-primary w-100 py-3 fw-bold"
                                                type="submit"
                                                disabled={processing || !hasResult}
                                            >
                                                {processing ? t('Generating...') : t('Generate invoice')} →
                                            </button>
                                        </div>

                                        <p className="text-center text-muted mb-4" style={{ fontSize: 13 }}>
                                            {t('Secure and fast currency exchange with the best daily rates')}.
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </>
    );
}