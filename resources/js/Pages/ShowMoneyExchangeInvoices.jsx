import { useState, useRef } from 'react';
import { usePage } from '@inertiajs/react';

// ─── Brand colors ──────────────────────────────────────────────────────────
const C = {
    primary:    '#5B2D8E',
    secondary:  '#9B59B6',
    light:      '#F5F0FA',
    dark:       '#1A0A2E',
    darker:     '#120720',
    cardBg:     '#261050',
    border:     'rgba(155,89,182,0.28)',
    borderSoft: 'rgba(155,89,182,0.13)',
    textMuted:  '#C3A8DC',
    textFaint:  '#8B6BAE',
    accent:     '#D4A8F0',
    green:      '#7EECC4',
    amber:      '#F5C842',
    white:      '#FFFFFF',
};

// ─── Utility ───────────────────────────────────────────────────────────────
const fmt = (n, d = 2) =>
    Number(n ?? 0).toFixed(d).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

// ─── Print CSS  (80 mm thermal) ────────────────────────────────────────────
const PRINT_STYLE = `
@media print {
  @page { size: 80mm auto; margin: 0; }
  body * { visibility: hidden !important; }
  #receipt-root, #receipt-root * { visibility: visible !important; }
  #receipt-root {
    position: fixed !important; inset: 0 !important;
    width: 80mm !important; margin: 0 auto !important;
    padding: 0 !important; background: #fff !important;
    font-family: 'Courier New', monospace !important;
    font-size: 11px !important; color: #000 !important;
    box-shadow: none !important; border-radius: 0 !important;
  }
  .no-print { display: none !important; }
}
`;

// ═══════════════════════════════════════════════════════════════════════════
export default function ShowMoneyExchangeInvoices({ records }) {
    const { translations } = usePage().props;
    const t = (key) => translations[key] ?? key;

    const { flash } = usePage().props;
    const [flashMsg, setFlashMsg] = useState(flash?.greet ?? null);
    const receiptRef = useRef(null);
    if (flashMsg) setTimeout(() => setFlashMsg(null), 5000);

    const invoices = Array.isArray(records) ? records : records ? [records] : [];
    const [selectedIdx, setSelectedIdx] = useState(0);
    const inv = invoices[selectedIdx] ?? {};

    const subtotal      = parseFloat(inv.subtotal      ?? inv.final_amount ?? 0);
    const serviceFee    = parseFloat(inv.service_fee   ?? 0);
    const finalAmount   = parseFloat(inv.final_amount  ?? subtotal);
    const enteredAmount = parseFloat(inv.entered_amount ?? 0);
    const exchangeRate  = parseFloat(inv.exchange_rate  ?? 0);
    const fromCurrency  = inv.from_currency ?? 'USD';
    const toCurrency    = inv.to_currency   ?? 'THB';
    const invoiceNo     = inv.invoice_number ?? inv.id ?? '—';
    const dateStr = inv.created_at
        ? new Date(inv.created_at).toLocaleDateString('en-GB',
              { day:'2-digit', month:'2-digit', year:'numeric' }).replace(/\//g,'-')
        : '—';
    const timeStr = inv.created_at
        ? new Date(inv.created_at).toLocaleTimeString('en-US',
              { hour:'2-digit', minute:'2-digit', hour12:true })
        : '';

    const sym = (c) =>
        ({ Dollar:'$', USD:'$', Baht:'฿', THB:'฿', KHR:'៛', Riel:'៛' }[c] ?? c);

    const handlePrint = () => window.print();

    const handleDownload = async () => {
        if (!window.html2canvas) {
            const s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            document.head.appendChild(s);
            await new Promise(r => (s.onload = r));
        }
        const canvas = await window.html2canvas(receiptRef.current,
            { scale:3, backgroundColor:'#ffffff', useCORS:true });
        const a = document.createElement('a');
        a.download = `invoice-${invoiceNo}.png`;
        a.href = canvas.toDataURL('image/png');
        a.click();
    };

    return (
        <>
            <style>{PRINT_STYLE}</style>
            <br/><br/>
           
            {/* ── Page shell ── */}
            <div className="container-fluid py-5" style={{
                minHeight: '100vh',
                background: `linear-gradient(160deg, ${C.darker} 0%, ${C.dark} 55%, #2D1060 100%)`,
                fontFamily: "'Segoe UI', sans-serif",
                padding: '28px 16px 60px',
            }}><br/>

                {/* Flash */}
                {flashMsg && (
                    <div style={{
                        maxWidth:660, margin:'0 auto 16px',
                        background:'rgba(126,236,196,0.10)', border:`1px solid ${C.green}`,
                        borderRadius:8, padding:'10px 16px', color:C.green, fontSize:13,
                    }}>✓ {flashMsg}</div>
                )}

                {/* ── Top bar ── */}
                <div className="no-print" style={{
                    maxWidth:660, margin:'0 auto 22px',
                    display:'flex', alignItems:'center', gap:10, flexWrap:'wrap',
                }}>
                    <button
                        onClick={() => window.history.back()}
                        style={{
                            display:'flex', alignItems:'center', gap:6,
                            background:'transparent', border:`1px solid ${C.border}`,
                            borderRadius:8, padding:'8px 16px',
                            color:C.accent, cursor:'pointer', fontSize:13, fontWeight:500,
                            transition:'all .2s',
                        }}
                        onMouseOver={e => { e.currentTarget.style.background=C.primary; e.currentTarget.style.borderColor=C.primary; }}
                        onMouseOut={e =>  { e.currentTarget.style.background='transparent'; e.currentTarget.style.borderColor=C.border; }}
                    >⟵ {t('Back')}</button>

                    <span style={{ flex:1, color:C.textFaint, fontSize:13 }}>
                        ({t('Money Exchange')}) · ({t('Invoice')})
                    </span>

                    {invoices.length > 1 && (
                        <select value={selectedIdx} onChange={e => setSelectedIdx(+e.target.value)}
                            style={{ background:C.cardBg, border:`1px solid ${C.border}`,
                                borderRadius:8, padding:'7px 10px', color:C.accent, fontSize:12 }}>
                            {invoices.map((iv,i) => (
                                <option key={i} value={i} style={{ background:C.dark }}>
                                    {iv.invoice_number ?? `#${iv.id}`}
                                </option>
                            ))}
                        </select>
                    )}

                    <button onClick={handleDownload}
                        style={{ background:C.primary, border:'none', borderRadius:8,
                            padding:'8px 16px', color:'#fff', cursor:'pointer', fontSize:12, fontWeight:600 }}
                        onMouseOver={e => e.currentTarget.style.background=C.secondary}
                        onMouseOut={e =>  e.currentTarget.style.background=C.primary}
                    >⬇ {t('Save PNG')}</button>

                    <button onClick={handlePrint}
                        style={{ background:'#6D28D9', border:'none', borderRadius:8,
                            padding:'8px 16px', color:'#fff', cursor:'pointer', fontSize:12, fontWeight:600 }}
                        onMouseOver={e => e.currentTarget.style.background='#7C3AED'}
                        onMouseOut={e =>  e.currentTarget.style.background='#6D28D9'}
                    >🖨 {t('Print')}</button>
                </div>

                {/* ════════════  DESKTOP INVOICE CARD  ════════════ */}
                <div className="no-print" style={{
                    maxWidth:660, margin:'0 auto 32px',
                    background:C.cardBg,
                    border:`1px solid ${C.border}`,
                    borderRadius:16, overflow:'hidden',
                    boxShadow:`0 24px 64px rgba(0,0,0,0.55)`,
                }}>
                    {/* Header */}
                    <div style={{
                        background:`linear-gradient(100deg, ${C.primary} 0%, ${C.secondary} 100%)`,
                        padding:'22px 28px',
                        display:'flex', justifyContent:'space-between', alignItems:'flex-start',
                    }}>
                        <div>
                            <div style={{ color:'rgba(255,255,255,0.65)', fontSize:10.5, letterSpacing:2, textTransform:'uppercase', marginBottom:4 }}>
                                40 Poipet Resort CO, Ltd · ({t('Money Exchange')})
                            </div>
                            <div style={{ color:'#fff', fontSize:22, fontWeight:700, letterSpacing:0.5 }}>
                                ({t('Invoice')}) {invoiceNo}
                            </div>
                        </div>
                        <div style={{ textAlign:'right' }}>
                            <span style={{
                                background:'rgba(255,255,255,0.18)',
                                border:'1px solid rgba(255,255,255,0.3)',
                                color:'#fff', padding:'3px 12px', borderRadius:20,
                                fontSize:11, fontWeight:600,
                            }}>({t(inv.exchange_type ?? 'Normal')}) </span>
                            <div style={{ color:'rgba(255,255,255,0.65)', fontSize:11, marginTop:6 }}>
                                {dateStr} &nbsp; {timeStr}
                            </div>
                        </div>
                    </div>

                    {/* Divider shimmer */}
                    <div style={{ height:1, background:`linear-gradient(90deg, transparent, ${C.secondary}66, transparent)` }} />

                    <div style={{ padding:'24px 28px' }}>
                        {/* Info cards */}
                        <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:14, marginBottom:22 }}>
                            <InfoCard title={t('Customer')} icon="👤">
                                <InfoLine k={t('Name')}  v={inv.customer_name || '—'} />
                                <InfoLine k={t('Phone Number')} v={inv.phone || '—'} />
                            </InfoCard>
                            <InfoCard title={t('Exchange')} icon="💱">
                                <InfoLine k={t('From')} v={`${t(fromCurrency)} (${sym(fromCurrency)})`} />
                                <InfoLine k={t('To')}   v={`${t(toCurrency)} (${sym(toCurrency)})`} />
                                <InfoLine k={t('Via')}  v={t(inv.receive_type) ?? 'Cash'} />
                            </InfoCard>
                        </div>

                        {/* Line-item table */}
                        <table style={{ width:'100%', borderCollapse:'collapse', fontSize:13 }}>
                            <thead>
                                <tr style={{ background:`rgba(91,45,142,0.35)` }}>
                                    {[t('Description'), t('Amount'), t('Rate'), t('Total')].map((h,i) => (
                                        <th key={h} style={{
                                            padding:'10px 14px', textAlign:i===0?'left':'right',
                                            color:C.textMuted, fontSize:10.5, fontWeight:700,
                                            letterSpacing:0.8, textTransform:'uppercase',
                                            borderBottom:`1px solid ${C.border}`,
                                        }}>{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                <tr style={{ background:'rgba(155,89,182,0.07)' }}>
                                    <td style={dTd('left')}>
                                        <div style={{ color:C.white, fontWeight:600 }}>{t('Currency Exchange')}</div>
                                        <div style={{ color:C.textFaint, fontSize:11, marginTop:3 }}>
                                            {t(inv.where_to_send)} · {t(inv.receive_type)}
                                        </div>
                                    </td>
                                    <td style={dTd('right')}>
                                        <span style={{ color:C.accent }}>{sym(fromCurrency)}{fmt(enteredAmount)}</span>
                                    </td>
                                    <td style={dTd('right')}>
                                        <span style={{ color:C.textMuted }}>{fmt(exchangeRate,4)}</span>
                                    </td>
                                    <td style={dTd('right')}>
                                        <span style={{ color:C.green, fontWeight:700 }}>{sym(toCurrency)}{fmt(subtotal)}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {/* Summary */}
                        <div style={{ marginTop:16, paddingTop:16, borderTop:`1px solid ${C.borderSoft}` }}>
                            <SRow label={t('Subtotal')}    val={`${sym(toCurrency)} ${fmt(subtotal)}`} />
                            {serviceFee > 0 && (
                                <SRow label={t('Service Fee')} val={`${sym(toCurrency)} ${fmt(serviceFee)}`} accent={C.amber} />
                            )}
                            <SRow label={t('Total Payable')} val={`${sym(toCurrency)} ${fmt(finalAmount)}`} isTotal />
                        </div>

                        {/* Rate pill */}
                        <div style={{
                            marginTop:18, padding:'10px 14px',
                            background:`rgba(91,45,142,0.22)`,
                            border:`1px solid ${C.borderSoft}`,
                            borderRadius:8, color:C.textMuted, fontSize:12,
                            display:'flex', justifyContent:'space-between',
                        }}>
                            <span>{t('Exchange Rate')}</span>
                            <span style={{ color:C.accent, fontWeight:600 }}>
                                1 {sym(fromCurrency)} = {fmt(exchangeRate,4)} {sym(toCurrency)}
                            </span>
                        </div>
                    </div>
                </div>

                {/* ════════════  THERMAL RECEIPT  ════════════ */}
                <div id="receipt-root" ref={receiptRef} style={{
                    width:300, maxWidth:'100%', margin:'0 auto',
                    background:'#fff', borderRadius:4, padding:'18px 18px 22px',
                    boxShadow:`0 20px 60px rgba(0,0,0,0.6), 0 0 0 1px ${C.primary}44`,
                    fontFamily:"'Courier New', Courier, monospace",
                    fontSize:11, color:'#111', position:'relative',
                }}>
                    {/* Top purple strip */}
                    <div style={{
                        position:'absolute', top:0, left:0, right:0, height:4,
                        background:`linear-gradient(90deg,${C.primary},${C.secondary})`,
                        borderRadius:'4px 4px 0 0',
                    }} />

                    {/* Store header */}
                    <div style={{ textAlign:'center', marginTop:10, marginBottom:10 }}>
                        <div style={{ fontSize:15, fontWeight:700, letterSpacing:2, color:C.primary }}>
                            40 Poipet Resort co Ltd
                        </div>
                        <div style={{ fontSize:9, color:'#666', lineHeight:1.55, marginTop:3 }}>
                            {t('Money Exchange')}<br />
                            Akia Thmey, beer city, PoiPet<br />
                            Banteay Meanchey Province<br />
                            {t('Tel')}: 012 50048 / 099 996000<br />
                            {t('VAT')}: 1011600036622
                        </div>
                    </div>

                    <RDiv />

                    <div style={{ textAlign:'center', fontWeight:700, fontSize:11.5,
                        letterSpacing:1.5, color:C.primary, marginBottom:7 }}>
                        ─── {t('BILL OF EXCHANGE')} ───
                    </div>

                    <RRow label={t('Invoice')} value={invoiceNo} bold />
                    <RRow label={t('Date')}     value={dateStr} />
                    <RRow label={t('Time')}     value={timeStr} />
                    <RRow label={t('Type')}     value={t(inv.exchange_type) ?? 'Normal'} />
                    <RRow label={t('Direction')} value={t(inv.where_to_send) ?? '—'} />

                    <RDiv />

                    <div style={{ fontWeight:700, fontSize:9.5, color:C.primary, marginBottom:4, letterSpacing:1 }}>
                        {t('Customer Information')}
                    </div>
                    <RRow label={t('Name')}  value={inv.customer_name || '—'} />
                    <RRow label={t('Phone Number')} value={inv.phone || '—'} />

                    <RDiv dashed />

                    <table style={{ width:'100%', borderCollapse:'collapse', fontSize:10 }}>
                        <thead>
                            <tr>
                                <th style={rTh('left')}>{t('Description')}</th>
                                <th style={rTh('right')}>{t(fromCurrency)}</th>
                                <th style={rTh('right')}>{t('Rate')}</th>
                                <th style={rTh('right')}>{t(toCurrency)}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style={rTd('left')}>
                                    {t('Exchange')}<br />
                                    <span style={{ fontSize:8.5, color:'#888' }}>{t(fromCurrency)}→{t(toCurrency)}</span>
                                </td>
                                <td style={rTd('right')}>{fmt(enteredAmount)}</td>
                                <td style={rTd('right')}>{fmt(exchangeRate,3)}</td>
                                <td style={rTd('right')}>{fmt(subtotal)}</td>
                            </tr>
                        </tbody>
                    </table>

                    <RDiv />

                    <RRow label={t('Subtotal')} value={`${sym(toCurrency)} ${fmt(subtotal)}`} />
                    {serviceFee > 0 && (
                        <RRow label={t('Service Fee')} value={`${sym(toCurrency)} ${fmt(serviceFee)}`} />
                    )}

                    {/* Grand total block */}
                    <div style={{
                        display:'flex', justifyContent:'space-between',
                        fontWeight:700, fontSize:13,
                        marginTop:6, padding:'6px 8px',
                        background:C.light,
                        borderLeft:`3px solid ${C.primary}`,
                        borderRadius:2, color:C.primary,
                    }}>
                        <span>{t('TOTAL')}</span>
                        <span>{sym(toCurrency)} {fmt(finalAmount)}</span>
                    </div>

                    <RDiv />

                    <RRow label={t('Payment')}  value={t(inv.receive_type) ?? 'Cash'} />
                    <RRow label={t('Ex. Rate')} value={`1${sym(fromCurrency)}=${fmt(exchangeRate,3)}${sym(toCurrency)}`} />

                    <RDiv dashed />

                    <div style={{ textAlign:'center', fontSize:9, color:'#888', lineHeight:1.8, marginTop:6 }}>
                        {t('Signature')} &amp; {t('Name of Seller')}<br />
                        <div style={{ borderTop:'1px solid #ccc', width:100, margin:'5px auto 6px' }} />
                        <span style={{ color:C.secondary, fontWeight:700 }}>{t('Thank you')}!</span><br />
                        {t('Please keep this receipt for your records')}.
                    </div>

                    {/* Bottom strip */}
                    <div style={{
                        position:'absolute', bottom:0, left:0, right:0, height:3,
                        background:`linear-gradient(90deg,${C.secondary},${C.primary})`,
                        borderRadius:'0 0 4px 4px',
                    }} />
                </div>

                <div style={{ height:40 }} />
            </div>
        </>
    );
}

// ─── Helpers ───────────────────────────────────────────────────────────────
function RDiv({ dashed }) {
    return <div style={{ borderTop:dashed?'1px dashed #ccc':'1px solid #bbb', margin:'7px 0' }} />;
}
function RRow({ label, value, bold }) {
    return (
        <div style={{ display:'flex', justifyContent:'space-between', fontSize:10.5, marginBottom:2, fontWeight:bold?700:400 }}>
            <span style={{ color:'#666' }}>{label}:</span>
            <span style={{ maxWidth:'62%', textAlign:'right', wordBreak:'break-word' }}>{value}</span>
        </div>
    );
}
const rTh = (a) => ({ padding:'3px', textAlign:a, fontWeight:700, fontSize:9.5, borderBottom:'1px solid #aaa', color:'#333' });
const rTd = (a) => ({ padding:'4px 3px', textAlign:a, verticalAlign:'top', fontSize:10 });
const dTd = (a) => ({ padding:'13px 14px', textAlign:a, borderBottom:`1px solid rgba(155,89,182,0.1)`, verticalAlign:'middle' });

function InfoCard({ title, icon, children }) {
    return (
        <div style={{ background:'rgba(91,45,142,0.2)', border:`1px solid rgba(155,89,182,0.22)`, borderRadius:10, padding:'13px 15px' }}>
            <div style={{ color:'#C3A8DC', fontSize:10, fontWeight:700, letterSpacing:1, textTransform:'uppercase', marginBottom:9 }}>
                {icon} {title}
            </div>
            {children}
        </div>
    );
}
function InfoLine({ k, v }) {
    return (
        <div style={{ display:'flex', justifyContent:'space-between', marginBottom:5, fontSize:12 }}>
            <span style={{ color:'#8B6BAE' }}>{k}</span>
            <span style={{ color:'#F5F0FA', fontWeight:500 }}>{v}</span>
        </div>
    );
}
function SRow({ label, val, isTotal, accent }) {
    return (
        <div style={{
            display:'flex', justifyContent:'space-between',
            padding:isTotal?'11px 0 0':'4px 0',
            marginTop:isTotal?8:0,
            borderTop:isTotal?`1px solid rgba(155,89,182,0.3)`:'none',
            fontSize:isTotal?16:13, fontWeight:isTotal?700:400,
        }}>
            <span style={{ color:isTotal?'#F5F0FA':'#8B6BAE' }}>{label}</span>
            <span style={{ color:isTotal?'#7EECC4':(accent??'#C3A8DC') }}>{val}</span>
        </div>
    );
}