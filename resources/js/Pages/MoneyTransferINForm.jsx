import { useState, useEffect, useRef } from "react";
import { useForm, usePage } from "@inertiajs/react";

// Bank list — filenames exactly as seen in the bank-images folder
const BANKS = [
    { symbol: "BBL",    value: "002", name: "Bangkok Bank",                         file: "BBL.png"    },
    { symbol: "KBANK",  value: "004", name: "Kasikorn Bank",                        file: "KBANK.png"  },
    { symbol: "KTB",    value: "006", name: "Krungthai Bank",                       file: "KTB.png"    },
    { symbol: "TMB",    value: "011", name: "TMB Thanachart Bank",                  file: "TMB.png"    },
    { symbol: "SCB",    value: "014", name: "Siam Commercial Bank",                 file: "SCB.jpg"    },
    { symbol: "CITI",   value: "017", name: "Citibank N.A.",                        file: "CITI.jpg"   },
    { symbol: "SCBT",   value: "020", name: "Standard Chartered Bank (Thailand)",   file: "SCBT.png"   },
    { symbol: "CIMB",   value: "022", name: "CIMB Thai Bank",                       file: "CIMB.png"   },
    { symbol: "UOB",    value: "024", name: "UOB Bank",                              file: "UOB.png"    },
    { symbol: "BAY",    value: "025", name: "Bank of Ayudhya",                      file: "BAY.jpg"    },
    { symbol: "GOV",    value: "030", name: "Government Savings Bank",              file: "GOV.jpg"    },
    { symbol: "GHB",    value: "033", name: "Government Housing Bank",              file: "GHB.jpg"    },
    { symbol: "AGRI",   value: "034", name: "Bank for Agriculture (BAAC)",          file: "AGRI.png"   },
    { symbol: "ISBT",   value: "066", name: "Islamic Bank of Thailand",             file: "ISBT.jpg"   },
    { symbol: "TISCO",  value: "067", name: "TISCO Bank",                           file: "TISCO.png"  },
    { symbol: "KK",     value: "069", name: "Kiatnakin Bank",                       file: "KK.jpg"     },
    { symbol: "ACL",    value: "070", name: "ACL (ACLEDA) Bank",                   file: "ACL.png"    },
    { symbol: "TCRB",   value: "071", name: "Thai Credit Retail Bank",             file: "TCRB.png"   },
    { symbol: "LHBANK", value: "073", name: "Land and House Bank",                 file: "LHBANK.jpg" },
];

const AMOUNT_SUGGESTIONS = [
    500, 1000, 1500, 2000, 2500, 3000, 4000, 5000,
    7500, 10000, 15000, 20000, 25000, 30000, 50000, 75000, 100000,
];

/* Real bank image — falls back to a coloured initial circle if the image fails */
function BankImg({ bank, size = 34, appUrl }) {
    const [errored, setErrored] = useState(false);

    if (errored) {
        return (
            <div style={{
                width: size, height: size, borderRadius: "50%",
                background: "#5B2D8E",
                display: "flex", alignItems: "center", justifyContent: "center",
                flexShrink: 0,
                fontSize: Math.max(8, size * 0.28) + "px",
                fontWeight: "800", color: "#fff", letterSpacing: "-0.5px",
            }}>
                {bank.symbol.slice(0, 3)}
            </div>
        );
    }

    return (
        <img
            src={`${appUrl}/website/assets/bank-images/${bank.file}`}
            alt={bank.name}
            onError={() => setErrored(true)}
            style={{
                width: size, height: size,
                borderRadius: "50%",
                objectFit: "cover",
                flexShrink: 0,
                border: "1.5px solid #e8ddf5",
                background: "#f8f5ff",
            }}
        />
    );
}

export default function MoneyTransferINForm({ gettransferchanges }) {
    // appUrl is shared via HandleInertiaRequests — add: 'appUrl' => config('app.url')
    const { appUrl, translations } = usePage().props;
    const t = (key) => translations?.[key] ?? key;

    const transferCharge = gettransferchanges?.[0] || null;
    const feePercentage  = transferCharge ? parseFloat(transferCharge.trf_fee_in_persentage) : 0;

    const { data, setData, post, processing, errors } = useForm({
        customer_name:         "",
        phone:                 "",
        bank_name:             "",
        bank_symbol:           "",
        acc_name:              "",
        acc_number:            "",
        entered_amount:        "",
        transfer_type:         "Transfer-IN",
        currency:              "THB",
        trf_fee_in_persentage: feePercentage,
        trf_fee:               "",
        net_amount:            "",
    });

    const [summary,      setSummary]      = useState(null);
    const [amountError,  setAmountError]  = useState("");
    const [dropOpen,     setDropOpen]     = useState(false);
    const [selectedBank, setSelectedBank] = useState(null);
    const [search,       setSearch]       = useState("");
    const dropRef = useRef(null);

    /* Close dropdown on outside click */
    useEffect(() => {
        const fn = (e) => {
            if (dropRef.current && !dropRef.current.contains(e.target)) {
                setDropOpen(false);
                setSearch("");
            }
        };
        document.addEventListener("mousedown", fn);
        return () => document.removeEventListener("mousedown", fn);
    }, []);

    /* Recalculate summary when amount changes */
    useEffect(() => {
        const amount = parseFloat(data.entered_amount);
        if (!isNaN(amount) && amount > 0) {
            const fee = (amount * feePercentage) / 100;
            const net = amount - fee;
            setSummary({ entered: amount, fee, net, feePercentage });
            setData(prev => ({ ...prev, trf_fee: fee.toFixed(2), net_amount: net.toFixed(2) }));
        } else {
            setSummary(null);
            setData(prev => ({ ...prev, trf_fee: "", net_amount: "" }));
        }
    }, [data.entered_amount]);

    const validateAmount = (val) => {
        const num = parseFloat(val);
        if (isNaN(num) || val === "") { setAmountError(""); return; }
        if (num < 500)          setAmountError("Minimum amount is ฿500 THB");
        else if (num > 100000)  setAmountError("Maximum amount is ฿100,000 THB");
        else                    setAmountError("");
    };

    const handleBankSelect = (bank) => {
        setSelectedBank(bank);
        setData(prev => ({ ...prev, bank_name: bank.name, bank_symbol: bank.symbol }));
        setDropOpen(false);
        setSearch("");
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post("/money-transfer-in/store");
    };

    const fmt = (v) =>
        new Intl.NumberFormat("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v);

    const filteredBanks = BANKS.filter(b =>
        b.name.toLowerCase().includes(search.toLowerCase()) ||
        b.symbol.toLowerCase().includes(search.toLowerCase())
    );

    const canSubmit = !processing && !amountError && data.entered_amount && data.bank_name && data.acc_name && data.acc_number;

    return (
        <div style={S.page}>
            <div style={S.orb1} />
            <div style={S.orb2} />

            <div style={S.card}>

                {/* ══ HEADER ══ */}
                <div style={S.header}>
                    <div style={S.hIcon}>
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h1 style={S.hTitle}>POIPET RESORT</h1>
                    <p style={S.hSub}>{t('Money Transfer')} - IN</p>
                    <span style={S.hBadge}>⬆ {t('Transfer')}-IN</span>
                </div>

                <form onSubmit={handleSubmit} style={S.form}>

                    {/* ── Row: Customer Name (optional) + Phone (optional) ── */}
                    <div style={S.row}>
                        <Field label={t('Customer Name')} optional t={t} style={{ flex: 1 }} error={errors.customer_name}>
                            <input
                                type="text"
                                value={data.customer_name}
                                onChange={e => setData("customer_name", e.target.value)}
                                placeholder={t('Full name')}
                                style={{ ...S.input, ...(errors.customer_name ? S.inputErr : {}) }}
                            />
                        </Field>
                        <Field label={t('Phone')} optional t={t} style={{ flex: 1 }}>
                            <input
                                type="text"
                                value={data.phone}
                                onChange={e => setData("phone", e.target.value)}
                                placeholder="08x-xxx-xxxx"
                                style={S.input}
                            />
                        </Field>
                    </div>

                    {/* ── Bank Name — custom image dropdown ── */}
                    <div style={S.fieldGroup} ref={dropRef}>
                        <label style={S.label}>{t('Bank Name')} <span style={S.req}>*</span></label>

                        {/* Trigger button */}
                        <div
                            style={{
                                ...S.dropTrigger,
                                ...(dropOpen ? S.dropTriggerOpen : {}),
                                ...(errors.bank_name ? S.inputErr : {}),
                            }}
                            onClick={() => { setDropOpen(o => !o); }}
                        >
                            <div style={S.dropInner}>
                                {selectedBank ? (
                                    <>
                                        <BankImg bank={selectedBank} size={32} appUrl={appUrl} />
                                        <span style={S.dropSelText}>{selectedBank.name}</span>
                                        <span style={S.dropBadge}>{selectedBank.symbol}</span>
                                    </>
                                ) : (
                                    <>
                                        <div style={S.dummyCircle}>
                                            <img
                                                src={`${appUrl}/website/assets/bank-images/dummy.png`}
                                                alt=""
                                                style={{ width: 18, height: 18, objectFit: "contain", opacity: 0.5 }}
                                                onError={e => { e.target.style.display = "none"; }}
                                            />
                                        </div>
                                        <span style={S.dropPlh}>{t('Select Bank')}</span>
                                    </>
                                )}
                            </div>
                            <span style={{ ...S.chevron, transform: dropOpen ? "rotate(180deg)" : "rotate(0)" }}>▾</span>
                        </div>

                        {/* Dropdown panel */}
                        {dropOpen && (
                            <div style={S.dropPanel}>
                                {/* Search bar */}
                                <div style={S.searchRow}>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9B59B6" strokeWidth="2.5">
                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    <input
                                        autoFocus
                                        type="text"
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        placeholder={t('Search bank…')}
                                        style={S.searchInput}
                                        onClick={e => e.stopPropagation()}
                                    />
                                </div>

                                {/* Bank list */}
                                <div style={S.dropScroll}>
                                    {filteredBanks.length === 0 ? (
                                        <div style={S.noResult}>{t('No bank found')}</div>
                                    ) : filteredBanks.map(bank => (
                                        <div
                                            key={bank.value}
                                            className="bank-row"
                                            style={{
                                                ...S.dropItem,
                                                ...(selectedBank?.value === bank.value ? S.dropItemActive : {}),
                                            }}
                                            onClick={() => handleBankSelect(bank)}
                                        >
                                            <BankImg bank={bank} size={38} appUrl={appUrl} />
                                            <div style={S.bankMeta}>
                                                <span style={S.bankNm}>{bank.name}</span>
                                                <span style={S.bankCd}>{bank.symbol} · {bank.value}</span>
                                            </div>
                                            {selectedBank?.value === bank.value && (
                                                <span style={S.tick}>✓</span>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                        {errors.bank_name && <span style={S.err}>{errors.bank_name}</span>}
                    </div>

                    {/* ── Account Name ── */}
                    <Field label={t('Account Name')} required t={t} error={errors.acc_name}>
                        <input
                            type="text"
                            value={data.acc_name}
                            onChange={e => setData("acc_name", e.target.value)}
                            placeholder={t('Enter bank account name')}
                            style={{ ...S.input, ...(errors.acc_name ? S.inputErr : {}) }}
                        />
                    </Field>

                    {/* ── Account Number ── */}
                    <Field label={t('Account Number')} required t={t} error={errors.acc_number}>
                        <input
                            type="text"
                            value={data.acc_number}
                            onChange={e => setData("acc_number", e.target.value)}
                            placeholder={t('Enter bank account number')}
                            style={{ ...S.input, ...(errors.acc_number ? S.inputErr : {}) }}
                        />
                    </Field>

                    {/* ── Amount ── */}
                    <div style={S.fieldGroup}>
                        <label style={S.label}>{t('Amount')} (THB) <span style={S.req}>*</span></label>
                        <div style={S.amtWrap}>
                            <span style={S.amtPfx}>฿</span>
                            <input
                                type="number"
                                list="amt-list"
                                value={data.entered_amount}
                                onChange={e => { setData("entered_amount", e.target.value); validateAmount(e.target.value); }}
                                placeholder="500 – 100,000"
                                min="500" max="100000" step="0.01"
                                style={{
                                    ...S.input,
                                    ...S.amtInput,
                                    ...(errors.entered_amount || amountError ? S.inputErr : {}),
                                }}
                            />
                            <datalist id="amt-list">
                                {AMOUNT_SUGGESTIONS.map(v => <option key={v} value={v} />)}
                            </datalist>
                        </div>
                        {(errors.entered_amount || amountError) && (
                            <span style={S.err}>{errors.entered_amount || amountError}</span>
                        )}
                        {/* Quick-pick pills */}
                        <div style={S.pills}>
                            {[500, 1000, 2000, 5000, 10000, 20000].map(v => (
                                <button
                                    key={v} type="button"
                                    style={{
                                        ...S.pill,
                                        ...(parseFloat(data.entered_amount) === v ? S.pillOn : {}),
                                    }}
                                    onClick={() => { setData("entered_amount", String(v)); validateAmount(String(v)); }}
                                >
                                    ฿{v.toLocaleString()}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* ── Transfer Summary ── */}
                    {summary && !amountError && (
                        <div style={S.summary}>
                            <div style={S.sumHead}>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#5B2D8E" strokeWidth="2.5">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                </svg>
                                <span style={S.sumTitle}>{t('Transfer Summary')}</span>
                            </div>
                            <div style={S.div} />
                            <SR label={t('Entered Amount')}                              val={`฿${fmt(summary.entered)} THB`} />
                            <SR label={`${t('Transfer Fee')} (${summary.feePercentage}%)`} val={`− ฿${fmt(summary.fee)} THB`} red />
                            <SR label={t('Service Charge')}                              val="0.00" />
                            <div style={S.div} />
                            <div style={{ ...S.sRow, paddingTop: 5 }}>
                                <span style={S.sumTotalLbl}>{t('Net Receive Amount')}</span>
                                <span style={S.sumTotal}>฿{fmt(summary.net)} THB</span>
                            </div>
                        </div>
                    )}

                    {/* ── Submit ── */}
                    <button
                        type="submit"
                        disabled={!canSubmit}
                        style={{ ...S.btn, ...(!canSubmit ? S.btnOff : {}) }}
                    >
                        {processing ? (
                            <><span style={S.spin} /> {t('Processing')}…</>
                        ) : (
                            <>
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                {t('Confirm')} / {t('Print')}
                            </>
                        )}
                    </button>
                </form>
            </div>

            <style>{`
                @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Playfair+Display:wght@700&display=swap');
                *, *::before, *::after { box-sizing: border-box; }
                input[type=number]::-webkit-inner-spin-button,
                input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
                input:focus { outline: none; border-color: #5B2D8E !important; box-shadow: 0 0 0 3px rgba(91,45,142,0.15); }
                input::placeholder { color: #c4b3d9; }
                .bank-row:hover { background: #f3ecfc !important; }
                @keyframes fadeUp  { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
                @keyframes popDown { from { opacity:0; transform:scaleY(.92) translateY(-6px); } to { opacity:1; transform:scaleY(1) translateY(0); } }
                @keyframes spin    { to { transform:rotate(360deg); } }
            `}</style>
        </div>
    );
}

/* ── Helpers ── */

/**
 * Field wrapper
 * - optional={true}  →  shows t('optional') in parentheses
 * - required={true}  →  shows * in primary colour
 */
function Field({ label, required, optional, t, error, children, style }) {
    return (
        <div style={{ ...S.fieldGroup, ...style }}>
            <label style={S.label}>
                {label}
                {required && <span style={S.req}> *</span>}
                {optional && (
                    <span style={S.opt}> ({t('optional')})</span>
                )}
            </label>
            {children}
            {error && <span style={S.err}>{error}</span>}
        </div>
    );
}

function SR({ label, val, red }) {
    return (
        <div style={S.sRow}>
            <span style={S.sLbl}>{label}</span>
            <span style={{ ...S.sVal, ...(red ? { color: "#dc2626", fontWeight: 700 } : {}) }}>{val}</span>
        </div>
    );
}

/* ─── Style map ─── */
const S = {
    page: {
        minHeight:"100vh",
        background:"linear-gradient(145deg,#F5F0FA 0%,#EAE0F5 55%,#F0EBF8 100%)",
        display:"flex", alignItems:"center", justifyContent:"center",
        padding:"40px 16px",
        fontFamily:"'Nunito',sans-serif",
        position:"relative", overflow:"hidden",
    },
    orb1:{ position:"fixed",top:"-140px",right:"-100px",width:"420px",height:"420px",borderRadius:"50%",background:"radial-gradient(circle,rgba(91,45,142,.16) 0%,transparent 70%)",pointerEvents:"none" },
    orb2:{ position:"fixed",bottom:"-120px",left:"-80px",width:"360px",height:"360px",borderRadius:"50%",background:"radial-gradient(circle,rgba(155,89,182,.12) 0%,transparent 70%)",pointerEvents:"none" },
    card:{
        background:"#fff", borderRadius:"24px",
        boxShadow:"0 12px 50px rgba(91,45,142,.14),0 2px 8px rgba(0,0,0,.05)",
        width:"100%", maxWidth:"560px",
        overflow:"visible",
        animation:"fadeUp .45s cubic-bezier(.22,.68,0,1.2) both",
        border:"1.5px solid rgba(91,45,142,.09)",
        marginTop:"90px",
    },
    header:{
        background:"linear-gradient(135deg,#3d1a72 0%,#5B2D8E 50%,#9B59B6 100%)",
        padding:"28px 36px 24px", textAlign:"center",
        borderRadius:"22px 22px 0 0",
    },
    hIcon:{ width:"48px",height:"48px",borderRadius:"13px",background:"rgba(255,255,255,.15)",display:"flex",alignItems:"center",justifyContent:"center",margin:"0 auto 10px",border:"1.5px solid rgba(255,255,255,.22)" },
    hTitle:{ fontFamily:"'Playfair Display',serif",fontSize:"23px",color:"#fff",letterSpacing:"3px",marginBottom:"3px" },
    hSub:{ color:"rgba(255,255,255,.72)",fontSize:"12px",letterSpacing:".4px",marginBottom:"10px" },
    hBadge:{ display:"inline-block",background:"rgba(255,255,255,.18)",color:"#fff",borderRadius:"999px",padding:"3px 13px",fontSize:"11px",fontWeight:"700",letterSpacing:".6px",border:"1px solid rgba(255,255,255,.28)" },

    form:{ padding:"24px 26px 30px",display:"flex",flexDirection:"column",gap:"15px" },
    row:{ display:"flex",gap:"12px" },
    fieldGroup:{ display:"flex",flexDirection:"column",gap:"5px",position:"relative" },
    label:{ fontSize:"12.5px",fontWeight:"700",color:"#2d1a4e",letterSpacing:".2px" },
    req:{ color:"#9B59B6" },
    opt:{ color:"#b8a8ce",fontWeight:"600",fontSize:"11px" },
    input:{
        border:"1.5px solid #E0D4EF",borderRadius:"10px",
        padding:"10px 13px",fontSize:"13.5px",color:"#1A0A2E",
        background:"#FDFBFF",transition:"border-color .2s,box-shadow .2s",
        width:"100%",fontFamily:"'Nunito',sans-serif",
    },
    // Avoid mixing border shorthand + borderColor — expand all four sides explicitly
    inputErr:{
        borderTopWidth:"1.5px",   borderTopStyle:"solid",   borderTopColor:"#ef4444",
        borderRightWidth:"1.5px", borderRightStyle:"solid", borderRightColor:"#ef4444",
        borderBottomWidth:"1.5px",borderBottomStyle:"solid",borderBottomColor:"#ef4444",
        borderLeftWidth:"1.5px",  borderLeftStyle:"solid",  borderLeftColor:"#ef4444",
        background:"#fef2f2",
    },
    err:{ fontSize:"11.5px",color:"#ef4444",fontWeight:"700" },

    // dropdown
    // No shorthand/longhand mixing — use only longhand border + borderRadius properties
    dropTrigger:{
        borderTopWidth:"1.5px",     borderTopStyle:"solid",     borderTopColor:"#E0D4EF",
        borderRightWidth:"1.5px",   borderRightStyle:"solid",   borderRightColor:"#E0D4EF",
        borderBottomWidth:"1.5px",  borderBottomStyle:"solid",  borderBottomColor:"#E0D4EF",
        borderLeftWidth:"1.5px",    borderLeftStyle:"solid",    borderLeftColor:"#E0D4EF",
        borderTopLeftRadius:"10px", borderTopRightRadius:"10px",
        borderBottomLeftRadius:"10px", borderBottomRightRadius:"10px",
        padding:"7px 12px",background:"#FDFBFF",
        cursor:"pointer",
        display:"flex",alignItems:"center",justifyContent:"space-between",
        transition:"border-top-color .2s,border-right-color .2s,border-bottom-color .2s,border-left-color .2s,box-shadow .2s",
        userSelect:"none",minHeight:"46px",
    },
    dropTriggerOpen:{
        borderTopColor:"#5B2D8E",    borderRightColor:"#5B2D8E",
        borderBottomColor:"#5B2D8E", borderLeftColor:"#5B2D8E",
        boxShadow:"0 0 0 3px rgba(91,45,142,.15)",
        borderBottomLeftRadius:0,    borderBottomRightRadius:0,
    },
    dropInner:{ display:"flex",alignItems:"center",gap:"10px",flex:1 },
    dropSelText:{ fontSize:"13.5px",fontWeight:"700",color:"#1A0A2E",flex:1 },
    dropPlh:{ color:"#c4b3d9",fontSize:"13.5px" },
    dropBadge:{ fontSize:"10.5px",color:"#9B59B6",fontWeight:"700",background:"#F0E8FA",padding:"2px 7px",borderRadius:"999px" },
    dummyCircle:{ width:"32px",height:"32px",borderRadius:"50%",background:"#F0E8FA",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0 },
    chevron:{ color:"#9B59B6",fontSize:"18px",transition:"transform .2s ease",lineHeight:1 },

    dropPanel:{
        position:"absolute",top:"100%",left:0,right:0,
        background:"#fff",
        border:"1.5px solid #5B2D8E",borderTop:"none",
        borderRadius:"0 0 14px 14px",
        boxShadow:"0 16px 40px rgba(91,45,142,.20)",
        zIndex:999,
        animation:"popDown .18s ease both",
        overflow:"hidden",
    },
    searchRow:{ display:"flex",alignItems:"center",gap:"8px",padding:"10px 14px",borderBottom:"1px solid #f0e8fa",background:"#faf7fd" },
    searchInput:{ border:"none",outline:"none",background:"transparent",fontSize:"13px",color:"#1A0A2E",flex:1,fontFamily:"'Nunito',sans-serif" },
    dropScroll:{ maxHeight:"260px",overflowY:"auto" },
    dropItem:{ display:"flex",alignItems:"center",gap:"12px",padding:"9px 14px",cursor:"pointer",transition:"background .12s",borderBottom:"1px solid #f5f0fa" },
    dropItemActive:{ background:"#F5F0FA" },
    bankMeta:{ display:"flex",flexDirection:"column",flex:1 },
    bankNm:{ fontSize:"13px",fontWeight:"700",color:"#1A0A2E" },
    bankCd:{ fontSize:"10.5px",color:"#9B59B6",marginTop:"1px" },
    tick:{ color:"#5B2D8E",fontWeight:"800",fontSize:"15px" },
    noResult:{ padding:"16px",textAlign:"center",color:"#b8a8ce",fontSize:"13px" },

    // amount
    amtWrap:{ position:"relative" },
    amtPfx:{ position:"absolute",left:"13px",top:"50%",transform:"translateY(-50%)",color:"#5B2D8E",fontWeight:"800",fontSize:"15px",zIndex:1 },
    amtInput:{ paddingLeft:"28px" },
    pills:{ display:"flex",flexWrap:"wrap",gap:"6px",marginTop:"6px" },
    pill:{
        padding:"4px 10px",borderRadius:"999px",
        borderTopWidth:"1.5px",   borderTopStyle:"solid",   borderTopColor:"#E0D4EF",
        borderRightWidth:"1.5px", borderRightStyle:"solid", borderRightColor:"#E0D4EF",
        borderBottomWidth:"1.5px",borderBottomStyle:"solid",borderBottomColor:"#E0D4EF",
        borderLeftWidth:"1.5px",  borderLeftStyle:"solid",  borderLeftColor:"#E0D4EF",
        background:"#FDFBFF",color:"#5B2D8E",fontSize:"11.5px",fontWeight:"700",
        cursor:"pointer",transition:"all .15s",fontFamily:"'Nunito',sans-serif",
    },
    pillOn:{
        background:"#5B2D8E",color:"#fff",
        borderTopColor:"#5B2D8E", borderRightColor:"#5B2D8E",
        borderBottomColor:"#5B2D8E", borderLeftColor:"#5B2D8E",
    },

    // summary
    summary:{ background:"linear-gradient(135deg,#F8F4FD 0%,#EDE0F7 100%)",borderRadius:"12px",padding:"14px 18px",border:"1.5px solid rgba(91,45,142,.14)",animation:"fadeUp .3s ease both" },
    sumHead:{ display:"flex",alignItems:"center",gap:"7px",marginBottom:"9px" },
    sumTitle:{ fontWeight:"800",color:"#5B2D8E",fontSize:"13px" },
    div:{ height:"1px",background:"rgba(91,45,142,.13)",margin:"7px 0" },
    sRow:{ display:"flex",justifyContent:"space-between",alignItems:"center",padding:"3px 0" },
    sLbl:{ fontSize:"12.5px",color:"#7a5a9a" },
    sVal:{ fontSize:"12.5px",color:"#1A0A2E",fontWeight:"600" },
    sumTotalLbl:{ fontSize:"13.5px",fontWeight:"800",color:"#1A0A2E" },
    sumTotal:{ fontSize:"19px",fontWeight:"800",color:"#5B2D8E",letterSpacing:"-.5px" },

    // submit
    btn:{ marginTop:"4px",background:"linear-gradient(135deg,#4a2280 0%,#5B2D8E 50%,#9B59B6 100%)",color:"#fff",border:"none",borderRadius:"12px",padding:"14px 24px",fontSize:"14.5px",fontWeight:"800",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",gap:"9px",letterSpacing:".4px",boxShadow:"0 5px 22px rgba(91,45,142,.38)",transition:"opacity .2s,box-shadow .2s",fontFamily:"'Nunito',sans-serif" },
    btnOff:{ opacity:.42,cursor:"not-allowed",boxShadow:"none" },
    spin:{ width:"16px",height:"16px",border:"2.5px solid rgba(255,255,255,.35)",borderTopColor:"#fff",borderRadius:"50%",display:"inline-block",animation:"spin .7s linear infinite" },
};