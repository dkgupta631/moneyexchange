import { useEffect, useState } from "react";

export default function Home({Leftrecords, Rightrecords}) {
    console.log(Leftrecords);

    // For Change Background color code Start
  const colors = [
    "#ffffff",
    "#f8f9fa",
    "#fff3cd",
    "#e8f5e9",
    "#e3f2fd",
    "#f3e5f5"
  ];

  const [bg, setBg] = useState(colors[0]);

  // change board background every 5 minutes START
    useEffect(() => {
        const changeColor = () => {
            const random = colors[Math.floor(Math.random() * colors.length)];
            setBg(random);
        };
        changeColor();
        const interval = setInterval(changeColor, 300000); // 5 minutes
        return () => clearInterval(interval);
    }, []);
  // change board background every 5 minutes END

    // for dateTime format code START
        const now = new Date().toLocaleString('en-GB', {
            timeZone: 'Asia/Bangkok',
            day: '2-digit',
            month: '2-digit',
            year: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
            });

        const formatted = now.replace(/\//g, '-').replace(',', '');
        
    // for dateTime format code END

     const capitalizeFirst = (text) => {
    if (!text) return ''
        return text.charAt(0).toUpperCase() + text.slice(1)
    }

  return (
    <>
    <div className="board-wrapper">

      <div className="exchange-board" style={{ background: bg }}>

        <h2 className="title">House 40 exchange rate</h2>
        <div className="city">Poipet City</div>

        <div className="top-info">
          <span className="time-css"><b>{formatted}</b></span>
          <span>"Exchange rates fluctuate"</span>
        </div>

        {/* MAIN TWO SECTIONS */}
        <div className="rate-container">

          {/* LEFT SIDE */}
          <div className="left-section">
            <div className="header-row">
              <div>For sale</div><div>Buy</div>
            </div>

            {Leftrecords.map(row => (

                <div key={row.id} className="rate-row">
                <div>
                    <div>{row.from_currency} - {row.to_currency}</div>    {/*  for Sale */}
                    <b>{row.normal_sell_rate}</b>
                </div>
                <div>
                    <div>{row.to_currency} - {row.from_currency}</div>    {/*  for Buy */}
                    <b>{row.normal_buy_rate}</b>
                </div>
                </div>

            ))}

            {/* <div className="rate-row">
              <div>
                <div>Dollar - Riel</div>
                <b>4,018</b>
              </div>
              <div>
                <div>Riel - Dollar</div>
                <b>4,025</b>
              </div>
            </div>

            <div className="rate-row">
              <div>
                <div>Dollar - Baht</div>
                <b>31.10</b>
              </div>
              <div>
                <div>Baht - Dollar</div>
                <b>31.20</b>
              </div>
            </div>

            <div className="rate-row">
              <div>
                <div>Baht - Riel</div>
                <b>128.8</b>
              </div>
              <div>
                <div>Riel - Baht</div>
                <b>129.4</b>
              </div>
            </div> */}

            <div className="bottom-label">
              <span className="black-color">&nbsp;&nbsp;Rate in hours</span>
            </div>

          </div>

          {/* RIGHT SIDE */}
          <div className="right-section">

            <div className="right-title">Thai Baht</div>
            <div className="right-note">Please contact in advance</div>
            <div className="header-row">
              <div>For sale</div><div>Buy</div>
            </div>

             {Rightrecords.map(row => (

                <div key={row.id} className="rate-row">
                <div>
                    <div>{row.from_currency} - {row.to_currency}</div>    {/*  for Sale */}
                    <b>{row.standard_sell_rate}</b>
                </div>
                <div>
                    <div>{row.to_currency} - {row.from_currency}</div>    {/*  for Buy */}
                    <b>{row.standard_buy_rate}</b>
                </div>
                </div>

            ))}

            <div className="bottom-label">
              <span className="time-css"><b>
                                            {capitalizeFirst(
                                            new Date().toLocaleTimeString('en-GB', {
                                                timeZone: 'Asia/Bangkok',
                                                hour: 'numeric',
                                                minute: '2-digit',
                                                hour12: true
                                            }).toUpperCase()
                                            )}
                    </b></span>
            </div>

          </div>

        </div>

        <div className="footer">
          <div><h6>Note: Exchange rates vary.</h6></div>
          <div className="tel">TEL: 012-580487, 099-996000</div>
          <div><h6>Working hours: 6.30 am to 5.30 pm</h6></div>
        </div>

      </div>

    </div><br/><br/><br/>
    </>
  );
}