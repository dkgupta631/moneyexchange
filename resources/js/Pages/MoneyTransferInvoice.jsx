import { useRef } from "react";
import { usePage, Link } from "@inertiajs/react";

export default function MoneyTransferInvoice({ invoice }) {
    const { translations, appUrl } = usePage().props;
    const t = (key) => translations?.[key] ?? key;
    const receiptRef = useRef(null);

    /* ── Helpers ── */
    const fmt = (v) =>
        new Intl.NumberFormat("th-TH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(parseFloat(v) || 0);

    const fmtDate = (dateStr) => {
        if (!dateStr) return "—";
        const d = new Date(dateStr);
        return d.toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });
    };

    const fmtTime = (dateStr) => {
        if (!dateStr) return "—";
        const d = new Date(dateStr);
        return d.toLocaleTimeString("en-GB", {
            hour: "2-digit",
            minute: "2-digit",
            hour12: true,
        }).toUpperCase();
    };

    /* ── Print (thermal-friendly: white bg, no nav, tight layout) ── */
    const handlePrint = () => {
        const printContent = receiptRef.current?.innerHTML;
        if (!printContent) return;
        const win = window.open("", "_blank", "width=900,height=800");
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Transfer-IN Invoice ${invoice?.invoice_number ?? ""}</title>
                <style>
                    @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');

                    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

                    /* Screen: center the receipt on the white page */
                    html, body {
                        width: 100%;
                        min-height: 100%;
                        background: #f0f0f0;
                        display: flex;
                        justify-content: center;
                        align-items: flex-start;
                        padding: 30px 0;
                        font-family: 'Courier Prime', 'Courier New', monospace;
                        font-size: 12px;
                        color: #000;
                    }

                    /* The receipt itself — fixed 80mm width, centered */
                    .receipt-print-wrapper {
                        width: 80mm;
                        min-width: 80mm;
                        max-width: 80mm;
                        background: #fff;
                        padding: 16px 16px 20px;
                        box-shadow: 0 4px 24px rgba(0,0,0,0.18);
                        border-top: 4px solid #5B2D8E;
                    }

                    /* All inline styles from React come through — override for print clarity */
                    div[style], span[style], table[style], td[style], th[style] {
                        font-family: 'Courier Prime', 'Courier New', monospace !important;
                    }

                    /* Section title overrides */
                    [style*="text-transform: uppercase"],
                    [style*="textTransform"] {
                        color: #5B2D8E !important;
                        font-weight: 700 !important;
                        font-size: 10px !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.8px !important;
                        margin: 6px 0 3px !important;
                    }

                    /* Tables */
                    table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
                    th { border-bottom: 1px solid #999 !important; padding: 3px 2px !important; }
                    td { padding: 3px 2px !important; }

                    /* Dashed dividers */
                    [style*="dashed"] { border-top: 1px dashed #bbb !important; margin: 7px 0 !important; }

                    /* Total row */
                    [style*="border-left: 3px solid"] {
                        background: #f5f0fa !important;
                        border-left: 3px solid #5B2D8E !important;
                        padding: 6px 8px !important;
                    }

                    /* Purple banner at top */
                    [style*="gradient"] {
                        background: linear-gradient(135deg,#3d1a72 0%,#5B2D8E 50%,#9B59B6 100%) !important;
                        text-align: center !important;
                        padding: 14px 0 10px !important;
                        margin: -16px -16px 14px -16px !important;
                    }

                    /* SVG inside banner */
                    [style*="gradient"] svg { display: block; margin: 0 auto; }

                    @media print {
                        @page {
                            size: 80mm auto;
                            margin: 0;
                        }
                        html, body {
                            background: #fff !important;
                            padding: 0 !important;
                            display: block !important;
                            width: 80mm !important;
                        }
                        .receipt-print-wrapper {
                            box-shadow: none !important;
                            border-top: 4px solid #5B2D8E !important;
                            width: 80mm !important;
                            max-width: 80mm !important;
                            padding: 10px 12px 14px !important;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="receipt-print-wrapper">
                    ${printContent}
                </div>
            </body>
            </html>
        `);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); }, 600);
    };

    /* ── Save as PNG using html2canvas (loaded from CDN) ── */
    const handleSavePNG = async () => {
        if (!receiptRef.current) return;
        const script = document.createElement("script");
        script.src = "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js";
        script.onload = async () => {
            const canvas = await window.html2canvas(receiptRef.current, {
                backgroundColor: "#ffffff",
                scale: 2,
                useCORS: true,
            });
            const link = document.createElement("a");
            link.download = `transfer-in-${invoice?.invoice_number ?? "invoice"}.png`;
            link.href = canvas.toDataURL("image/png");
            link.click();
        };
        document.head.appendChild(script);
    };

    const inv = invoice || {};

    return (
        <div style={S.page}><br/><br/><br/><br/>
            {/* ── Top action bar ── */}
            <div style={S.topBar}>
                <Link href="/money-transfer-in" style={S.backBtn}>
                    ← {t("Back")}
                </Link>
                <span style={S.breadcrumb}>
                    ({t("Money Transfer")}) · ({t("Invoice")})
                </span>
                <div style={S.actions}>
                    <button style={S.savePngBtn} onClick={handleSavePNG}>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        {t("Save PNG")}
                    </button>
                    <button style={S.printBtn} onClick={handlePrint}>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        {t("Print")}
                    </button>
                </div>
            </div>

            {/* ── Receipt card (this is what gets printed / saved) ── */}
            <div style={S.receiptWrap}>
                <div ref={receiptRef} style={S.receipt} className="receipt">

                    {/* Header */}
                    <div style={S.receiptHeaderBanner}>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div style={{ textAlign: "center", marginBottom: 10 }}>
                        <div style={S.logoText}>G+ Services</div>
                        <div style={S.subText}>{t("Money Transfer Service")}</div>
                        <div style={S.subText}>Aria Thmey, PoiPet</div>
                        <div style={S.subText}>Banteay Meanchey Province</div>
                    </div>

                    <div style={S.dashed} />

                    <div className="center" style={S.billTitle}>
                        — {t("TRANSFER-IN RECEIPT")} —
                    </div>

                    {/* Invoice meta */}
                    <div style={S.metaSection}>
                        <Row label={t("Invoice")}  value={inv.invoice_number ?? "—"} bold />
                        <Row label={t("Date")}      value={fmtDate(inv.created_at)} />
                        <Row label={t("Time")}      value={fmtTime(inv.created_at)} />
                        <Row label={t("Type")}      value={t("Transfer-IN")} />
                    </div>

                    <div style={S.dashed} />

                    {/* Customer info */}
                    <div style={S.sectionTitle}>{t("Customer Information")}</div>
                    <Row label={t("Name")}         value={inv.customer_name || "—"} />
                    <Row label={t("Phone Number")} value={inv.phone || "—"} />

                    <div style={S.dashed} />

                    {/* Bank info */}
                    <div style={S.sectionTitle}>{t("Bank Details")}</div>
                    <Row label={t("Bank")}           value={inv.bank_name || "—"} />
                    <Row label={t("Account Name")}   value={inv.acc_name  || "—"} />
                    <Row label={t("Account Number")} value={inv.acc_number || "—"} />

                    <div style={S.dashed} />

                    {/* Amount table */}
                    <table style={S.table}>
                        <thead>
                            <tr>
                                <th style={S.th}>{t("Description")}</th>
                                <th style={{ ...S.th, textAlign: "right" }}>{t("Amount")}</th>
                                <th style={{ ...S.th, textAlign: "right" }}>{t("Fee %")}</th>
                                <th style={{ ...S.th, textAlign: "right" }}>{t("Net")}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style={S.td}>
                                    <strong>{t("Transfer")}</strong><br/>
                                    <span style={S.subText2}>{t("Transfer-IN")} → THB</span>
                                </td>
                                <td style={{ ...S.td, textAlign: "right" }}>฿{fmt(inv.entered_amount)}</td>
                                <td style={{ ...S.td, textAlign: "right" }}>{inv.trf_fee_in_persentage ?? 0}%</td>
                                <td style={{ ...S.td, textAlign: "right" }}>฿{fmt(inv.net_amount)}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div style={S.dashed} />

                    {/* Totals */}
                    <Row label={t("Entered Amount")} value={`฿ ${fmt(inv.entered_amount)}`} />
                    <Row
                        label={`${t("Transfer Fee")} (${inv.trf_fee_in_persentage ?? 0}%)`}
                        value={`− ฿ ${fmt(inv.trf_fee)}`}
                        valueStyle={{ color: "#dc2626" }}
                    />
                    <Row label={t("Service Fee")} value="฿ 0.00" />

                    <div style={S.totalRow}>
                        <span style={S.totalLabel}>{t("NET AMOUNT")}</span>
                        <span style={S.totalValue}>฿ {fmt(inv.net_amount)}</span>
                    </div>

                    <div style={S.dashed} />

                    {/* Footer */}
                    <div style={S.footerSig}>{t("Signature & Name of Staff")}</div>
                    <div style={S.sigLine} />
                    <div style={S.thankYou}>{t("Thank you!")}</div>
                    <div style={S.keepReceipt}>
                        {t("Please keep this receipt for your records.")}
                    </div>

                </div>
            </div>

            <style>{`
                @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Nunito:wght@600;700;800&display=swap');
                * { box-sizing: border-box; }
                @media print {
                    body * { visibility: hidden; }
                    .receipt, .receipt * { visibility: visible; }
                    .receipt { position: absolute; left: 0; top: 0; width: 80mm; }
                }
            `}</style>
        </div>
    );
}

/* ── Simple label/value row ── */
function Row({ label, value, bold, valueStyle }) {
    return (
        <div style={SR.row}>
            <span style={{ ...SR.label, ...(bold ? { fontWeight: 700 } : {}) }}>{label}:</span>
            <span style={{ ...SR.value, ...valueStyle }}>{value}</span>
        </div>
    );
}
const SR = {
    row:   { display: "flex", justifyContent: "space-between", alignItems: "flex-start", padding: "2px 0", gap: 8 },
    label: { fontSize: "11px", color: "#555", fontFamily: "'Courier Prime', monospace", whiteSpace: "nowrap" },
    value: { fontSize: "11px", color: "#111", fontFamily: "'Courier Prime', monospace", textAlign: "right", wordBreak: "break-all" },
};

/* ── Styles ── */
const S = {
    page: {
        minHeight: "100vh",
        background: "#1A0A2E",
        fontFamily: "'Nunito', sans-serif",
        padding: "24px 20px 48px",
    },
    topBar: {
        marginTop: "100px",
        display: "flex", alignItems: "center", gap: 50, flexWrap: "wrap",
        maxWidth: 680, margin: "0 auto 24px",
    },
    backBtn: {
        display: "inline-flex", alignItems: "center", gap: 6,
        padding: "7px 16px",
        borderRadius: 8,
        border: "1.5px solid rgba(255,255,255,0.25)",
        color: "#fff",
        fontSize: 13, fontWeight: 700,
        textDecoration: "none",
        background: "rgba(255,255,255,0.07)",
        transition: "background .15s",
    },
    breadcrumb: {
        color: "rgba(255,255,255,0.45)",
        fontSize: 13,
        flex: 1,
    },
    actions: { display: "flex", gap: 10 },
    savePngBtn: {
        display: "inline-flex", alignItems: "center", gap: 7,
        padding: "8px 16px", borderRadius: 9,
        background: "#5B2D8E", color: "#fff",
        border: "none", fontSize: 13, fontWeight: 700,
        cursor: "pointer",
        boxShadow: "0 3px 12px rgba(91,45,142,0.45)",
    },
    printBtn: {
        display: "inline-flex", alignItems: "center", gap: 7,
        padding: "8px 18px", borderRadius: 9,
        background: "linear-gradient(135deg,#5B2D8E,#9B59B6)",
        color: "#fff",
        border: "none", fontSize: 13, fontWeight: 700,
        cursor: "pointer",
        boxShadow: "0 3px 14px rgba(91,45,142,0.5)",
    },

    /* receipt paper */
    receiptWrap: {
        maxWidth: 400,
        margin: "0 auto",
        background: "#fff",
        borderRadius: 6,
        boxShadow: "0 0 0 1.5px rgba(91,45,142,0.35), 0 16px 48px rgba(0,0,0,0.55)",
        overflow: "hidden",
        borderTop: "4px solid #5B2D8E",
    },
    receipt: {
        padding: "20px 22px",
        fontFamily: "'Courier Prime', 'Courier New', monospace",
        fontSize: 11,
        color: "#111",
        background: "#fff",
    },
    receiptHeaderBanner: {
        background: "linear-gradient(135deg,#3d1a72 0%,#5B2D8E 50%,#9B59B6 100%)",
        margin: "-20px -22px 14px -22px",
        padding: "18px 0 14px",
        textAlign: "center",
    },
    logoText: {
        fontFamily: "'Courier Prime', monospace",
        fontSize: 18, fontWeight: 700,
        color: "#5B2D8E", letterSpacing: 3,
        marginBottom: 3,
        textAlign: "center",
        display: "block",
    },
    subText: {
        fontSize: 10, color: "#666",
        fontFamily: "'Courier Prime', monospace",
        lineHeight: 1.6,
        textAlign: "center",
        display: "block",
    },
    dashed: {
        borderTop: "1px dashed #bbb",
        margin: "8px 0",
    },
    billTitle: {
        fontSize: 12, fontWeight: 700,
        color: "#5B2D8E", letterSpacing: 1.5,
        marginBottom: 8, marginTop: 4,
        fontFamily: "'Courier Prime', monospace",
        textAlign: "center",
    },
    metaSection: { marginBottom: 4 },
    sectionTitle: {
        fontSize: 10, fontWeight: 700,
        color: "#5B2D8E",
        marginBottom: 4, marginTop: 2,
        fontFamily: "'Courier Prime', monospace",
        textTransform: "uppercase", letterSpacing: 0.8,
    },
    table: {
        width: "100%", borderCollapse: "collapse",
        margin: "4px 0",
    },
    th: {
        borderBottom: "1px solid #999",
        padding: "4px 2px",
        fontSize: 10, fontWeight: 700,
        color: "#333",
        fontFamily: "'Courier Prime', monospace",
        textAlign: "left",
    },
    td: {
        padding: "4px 2px",
        fontSize: 10,
        fontFamily: "'Courier Prime', monospace",
        verticalAlign: "top",
        color: "#111",
    },
    subText2: {
        fontSize: 9, color: "#888",
    },
    totalRow: {
        display: "flex", justifyContent: "space-between", alignItems: "center",
        background: "#f5f0fa",
        borderLeft: "3px solid #5B2D8E",
        padding: "7px 8px",
        marginTop: 6, marginBottom: 2,
    },
    totalLabel: {
        fontSize: 13, fontWeight: 700,
        color: "#1A0A2E",
        fontFamily: "'Courier Prime', monospace",
        letterSpacing: 0.5,
    },
    totalValue: {
        fontSize: 16, fontWeight: 700,
        color: "#5B2D8E",
        fontFamily: "'Courier Prime', monospace",
    },
    footerSig: {
        textAlign: "center",
        fontSize: 9, color: "#888",
        marginTop: 12, marginBottom: 4,
        fontFamily: "'Courier Prime', monospace",
    },
    sigLine: {
        borderTop: "1px solid #ccc",
        width: "60%", margin: "0 auto 8px",
    },
    thankYou: {
        textAlign: "center",
        fontSize: 11, fontWeight: 700,
        color: "#5B2D8E",
        fontFamily: "'Courier Prime', monospace",
        marginBottom: 2,
    },
    keepReceipt: {
        textAlign: "center",
        fontSize: 9, color: "#888",
        fontFamily: "'Courier Prime', monospace",
        marginBottom: 4,
    },
};