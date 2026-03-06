import { useEffect, useState } from "react";

export default function Home({Leftrecords, Rightrecords}) {
    console.log(Leftrecords);
    const appUrl = window.location.origin;

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

    {/* <!-- Service Start --> */}
        <div className="container-fluid py-5">
            <div className="container px-lg-5">
                <div className="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                    <h6 className="position-relative d-inline text-primary ps-4">Our Services</h6>
                    <h2 className="mt-2">What Solutions We Provide</h2>
                </div>
                <div className="row g-4">
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.1s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <i className="fa fa-chart-line fa-2x"></i>
                            </div>
                            <h5 className="mb-3">SEO Optimization</h5>
                            <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <i className="fa fa-laptop-code fa-2x"></i>
                            </div>
                            <h5 className="mb-3">Web Design</h5>
                            <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.6s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <i className="fa fa-share-alt fa-2x"></i>
                            </div>
                            <h5 className="mb-3">Social Media Marketing</h5>
                            <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.1s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <i className="fa fa-envelope-open-text fa-2x"></i>
                            </div>
                            <h5 className="mb-3">Email Marketing</h5>
                            <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <i className="fa fa-mouse-pointer fa-2x"></i>
                            </div>
                            <h5 className="mb-3">PPC Advertising</h5>
                            <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.6s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <i className="fa fa-mobile-alt fa-2x"></i>
                            </div>
                            <h5 className="mb-3">App Development</h5>
                            <p>Erat ipsum justo amet duo et elitr dolor, est duo duo eos lorem sed diam stet diam sed stet lorem.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/* <!-- Service End --> */}

        {/* <!-- Portfolio Start --> */}
         <div className="container-fluid py-5">
            <div className="container px-lg-5">

                <div className="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                    <h6 className="position-relative d-inline text-primary ps-4">Our Projects</h6>
                    <h2 className="mt-2">Recently Launched Projects</h2>
                </div>

                <div className="row g-4">

                    {/* Project 1 */}
                    <div className="col-lg-4 col-md-6 portfolio-item wow zoomIn" data-wow-delay="0.1s">
                        <div className="position-relative rounded overflow-hidden">
                            <img
                                className="img-fluid w-100"
                                src={`${appUrl}/website/assets/img/portfolio-1.jpg`}
                                alt=""
                            />
                            <div className="portfolio-overlay">
                                <a className="btn btn-light" href={`${appUrl}/website/assets/img/portfolio-1.jpg`}>
                                    <i className="fa fa-plus fa-2x text-primary"></i>
                                </a>
                                <div className="mt-auto">
                                    <small className="text-white">
                                        <i className="fa fa-folder me-2"></i>Web Design
                                    </small>
                                    <a className="h5 d-block text-white mt-1 mb-0" href="#">
                                        Project Name
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Project 2 */}
                    <div className="col-lg-4 col-md-6 portfolio-item wow zoomIn" data-wow-delay="0.3s">
                        <div className="position-relative rounded overflow-hidden">
                            <img
                                className="img-fluid w-100"
                                src={`${appUrl}/website/assets/img/portfolio-2.jpg`}
                                alt=""
                            />
                            <div className="portfolio-overlay">
                                <a className="btn btn-light" href={`${appUrl}/website/assets/img/portfolio-2.jpg`}>
                                    <i className="fa fa-plus fa-2x text-primary"></i>
                                </a>
                                <div className="mt-auto">
                                    <small className="text-white">
                                        <i className="fa fa-folder me-2"></i>Web Design
                                    </small>
                                    <a className="h5 d-block text-white mt-1 mb-0" href="#">
                                        Project Name
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Project 3 */}
                    <div className="col-lg-4 col-md-6 portfolio-item wow zoomIn" data-wow-delay="0.6s">
                        <div className="position-relative rounded overflow-hidden">
                            <img
                                className="img-fluid w-100"
                                src={`${appUrl}/website/assets/img/portfolio-3.jpg`}
                                alt=""
                            />
                            <div className="portfolio-overlay">
                                <a className="btn btn-light" href={`${appUrl}/website/assets/img/portfolio-3.jpg`}>
                                    <i className="fa fa-plus fa-2x text-primary"></i>
                                </a>
                                <div className="mt-auto">
                                    <small className="text-white">
                                        <i className="fa fa-folder me-2"></i>Web Design
                                    </small>
                                    <a className="h5 d-block text-white mt-1 mb-0" href="#">
                                        Project Name
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Project 4 */}
                    <div className="col-lg-4 col-md-6 portfolio-item wow zoomIn" data-wow-delay="0.1s">
                        <div className="position-relative rounded overflow-hidden">
                            <img
                                className="img-fluid w-100"
                                src={`${appUrl}/website/assets/img/portfolio-4.jpg`}
                                alt=""
                            />
                            <div className="portfolio-overlay">
                                <a className="btn btn-light" href={`${appUrl}/website/assets/img/portfolio-4.jpg`}>
                                    <i className="fa fa-plus fa-2x text-primary"></i>
                                </a>
                                <div className="mt-auto">
                                    <small className="text-white">
                                        <i className="fa fa-folder me-2"></i>Web Design
                                    </small>
                                    <a className="h5 d-block text-white mt-1 mb-0" href="#">
                                        Project Name
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Project 5 */}
                    <div className="col-lg-4 col-md-6 portfolio-item wow zoomIn" data-wow-delay="0.3s">
                        <div className="position-relative rounded overflow-hidden">
                            <img
                                className="img-fluid w-100"
                                src={`${appUrl}/website/assets/img/portfolio-5.jpg`}
                                alt=""
                            />
                            <div className="portfolio-overlay">
                                <a className="btn btn-light" href={`${appUrl}/website/assets/img/portfolio-5.jpg`}>
                                    <i className="fa fa-plus fa-2x text-primary"></i>
                                </a>
                                <div className="mt-auto">
                                    <small className="text-white">
                                        <i className="fa fa-folder me-2"></i>Web Design
                                    </small>
                                    <a className="h5 d-block text-white mt-1 mb-0" href="#">
                                        Project Name
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Project 6 */}
                    <div className="col-lg-4 col-md-6 portfolio-item wow zoomIn" data-wow-delay="0.6s">
                        <div className="position-relative rounded overflow-hidden">
                            <img
                                className="img-fluid w-100"
                                src={`${appUrl}/website/assets/img/portfolio-6.jpg`}
                                alt=""
                            />
                            <div className="portfolio-overlay">
                                <a className="btn btn-light" href={`${appUrl}/website/assets/img/portfolio-6.jpg`}>
                                    <i className="fa fa-plus fa-2x text-primary"></i>
                                </a>
                                <div className="mt-auto">
                                    <small className="text-white">
                                        <i className="fa fa-folder me-2"></i>Web Design
                                    </small>
                                    <a className="h5 d-block text-white mt-1 mb-0" href="#">
                                        Project Name
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        {/* <!-- Portfolio End --> */}
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