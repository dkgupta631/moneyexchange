import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

 export default function Home({records}) {
  // console.log(records);

//   const [boardBg, setBoardBg] = useState('');
//     // 🎨 Change board background daily
//     useEffect(() => {
//         const colors = [
//             'board-bg-orange',
//             'board-bg-blue',
//             'board-bg-green',
//             'board-bg-purple',
//             'board-bg-dark',
//         ];

//         const index = new Date().getDate() % colors.length;
//         setBoardBg(colors[index]);
//     }, []);

    const boardColors = [
        'board-bg-1',
        'board-bg-2',
        'board-bg-3',
        'board-bg-4',
        'board-bg-5',
    ];

    const getHourlyColor = () => {
        const hour = new Date().getHours();
        return boardColors[hour % boardColors.length];
    };

    const [boardBg, setBoardBg] = useState(getHourlyColor());

    useEffect(() => {
        const interval = setInterval(() => {
            setBoardBg(getHourlyColor());
        }, 60 * 60 * 1000); // every 1 hour

        return () => clearInterval(interval);
    }, []);

  return (
    <>
    <style>
            {`
              /* Full page wrapper */
.exchange-wrapper {
    // min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* 🔒 Exchange board */
.exchange-board {
    width: 380px;
    min-height: 720px;
    border-radius: 16px;
    padding: 22px 18px;
    font-family: Arial, sans-serif;
    box-shadow: 0 15px 40px rgba(0,0,0,0.35);
    color: #222;
    transition: background 0.6s ease;
}

/* Mobile = FULL WIDTH */
@media (max-width: 768px) {
    .exchange-wrapper {
        align-items: flex-start;
    }

    .exchange-board {
        width: 100%;
        min-height: 100vh;
        border-radius: 0;
    }
}

/* Typography */
.title {
    text-align: center;
    font-size: 22px;
    font-weight: 700;
}

.city {
    text-align: center;
    font-size: 14px;
}

.time {
    text-align: center;
    font-size: 13px;
    margin: 6px 0 10px;
}

.note-top {
    font-size: 12px;
    text-align: center;
    margin-bottom: 16px;
    opacity: 0.85;
}

/* Sections */
.rate-section {
    margin-top: 16px;
}

.rate-section h4 {
    font-size: 15px;
    margin-bottom: 8px;
}

/* Grid */
.rate-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.rate-grid div {
    background: rgba(255,255,255,0.85);
    border-radius: 10px;
    padding: 12px 8px;
    text-align: center;
}

.rate-grid small {
    font-size: 11px;
    color: #666;
}

.rate-grid p {
    font-size: 13px;
    margin: 4px 0;
}

.rate-grid span {
    font-size: 20px;
    font-weight: bold;
}

/* Footer */
.footer {
    text-align: center;
    margin-top: 18px;
    font-size: 13px;
}

.tel {
    color: red;
    font-weight: bold;
}

/* 🎨 HOURLY BOARD BACKGROUNDS */
.board-bg-1 { background: #fffaf0; }
.board-bg-2 { background: #f0f9ff; }
.board-bg-3 { background: #f5fff3; }
.board-bg-4 { background: #fff5f7; }
.board-bg-5 { background: #f4f4ff; }
              `}
      </style>
    {/* <Head title="Home" /> */}
    {/* <Head>
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
      </div> */}

        <div className="exchange-wrapper">
            <div className={`exchange-board ${boardBg}`}>
                <h2 className="title">Exchange Rate</h2>
                <p className="city">Poipet City</p>

                <div className="time">24-02-26 &nbsp; 8:30 AM</div>

                <p className="note-top">
                    “Exchange rates fluctuate – Please contact in advance”
                </p>

                {/* USD ↔ RIEL */}
                <div className="rate-section">
                    <h4>USD ↔ Riel</h4>
                    <div className="rate-grid">
                        <div>
                            <small>For Sale</small>
                            <p>Dollar → Riel</p>
                            <span>4,018</span>
                        </div>
                        <div>
                            <small>Buy</small>
                            <p>Riel → Dollar</p>
                            <span>4,025</span>
                        </div>
                    </div>
                </div>

                {/* USD ↔ BAHT */}
                <div className="rate-section">
                    <h4>USD ↔ Thai Baht</h4>
                    <div className="rate-grid">
                        <div>
                            <small>For Sale</small>
                            <p>Dollar → Baht</p>
                            <span>31.20</span>
                        </div>
                        <div>
                            <small>Buy</small>
                            <p>Baht → Dollar</p>
                            <span>32.20</span>
                        </div>
                    </div>
                </div>

                {/* BAHT ↔ RIEL */}
                <div className="rate-section">
                    <h4>Baht ↔ Riel</h4>
                    <div className="rate-grid">
                        <div>
                            <p>Baht → Riel</p>
                            <span>124.8</span>
                        </div>
                        <div>
                            <p>Riel → Baht</p>
                            <span>129.0</span>
                        </div>
                    </div>
                </div>

                <div className="footer">
                    <p><strong>Rate Time:</strong> 8:30 AM</p>
                    <p className="tel">TEL: 012-580487, 099-996000</p>
                    <p>Working hours: 6:30 AM – 5:30 PM</p>
                </div>
            </div>
        </div><br/><br/>

     
      
    </>
  );
}

