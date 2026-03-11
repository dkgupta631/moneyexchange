import { Head, Link, usePage } from '@inertiajs/react';
   

export default function Home() {
    const { translations, locale, ziggy } = usePage().props;
    const appUrl = window.location.origin;
    const t = (key) => translations[key] ?? key;

    const capitalizeFirst = (text) => {
    if (!text) return ''
        return text.charAt(0).toUpperCase() + text.slice(1)
    }

  return (
    <>
    <Head title={t('Home')}>
        <meta head-key="description" name="description" content={t('Exchange currency rates help customers convert money correctly, make secure payments, and ensure transparency and trust in international transactions')}/>
        <link rel="icon" type="image/svg+xml" href={`${appUrl}/website/assets/logo/logo.png`}/>
    </Head>
    {/* <!-- Service Start --> */}
        <div className="container-fluid py-10 mb-5 bg-primary hero-header">
            <div className="container px-lg-5 py-10"><br/><br/><br/><br/>
                <div className="section-title position-relative text-center mb-5 pb-2 wow fadeInUp" data-wow-delay="0.1s">
                    <h6 className="position-relative d-inline text-primary ps-4">Our Services</h6>
                    <h2 className="mt-2">What Solutions We Provide</h2>
                </div>
                <div className="row g-4">
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.1s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <Link href={route('showExchangeRate')}>
                            <div className="service-icon flex-shrink-0">
                                <img src={`${appUrl}/website/assets/img/money-exchange3.jpg`} style={{ height: '100px', borderRadius: '50px' }}/>
                            </div>
                            <h5 className="mb-3">Money Exchange</h5></Link>
                            <p>Money exchange services provide real-time exchange rates between currencies like USD, THB, KHR etc.</p>
                            <Link href={route('showExchangeRate')} className="btn px-3 mt-auto mx-auto">Read More</Link>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                        
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <Link href={route('open.moneyexchange.form')}>
                            <div className="service-icon flex-shrink-0">
                                <img src={`${appUrl}/website/assets/img/money-transfer2.jpg`} style={{ height: '100px', borderRadius: '50px' }}/>
                            </div>
                            <h5 className="mb-3">Money Transfer</h5></Link>
                            <p>Money can be transferred to different countries and currencies easily. This helps people send money to family, friends, or businesses internationally.</p>
                            <Link href={route('open.moneyexchange.form')} className="btn px-3 mt-auto mx-auto">Read More</Link>
                        </div>
                        
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.6s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <img src={`${appUrl}/website/assets/img/cash-transfer.jpg`} style={{ height: '100px', borderRadius: '50px' }}/>
                            </div>
                            <h5 className="mb-3">Cash Transfer</h5>
                            <p>Customers only need basic details like the receiver’s name and phone number. This makes sending money simple and convenient.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.1s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                {/* <i className="fa fa-envelope-open-text fa-2x"></i> */}
                                <img src={`${appUrl}/website/assets/img/withdraw-money2.jpg`} style={{ height: '100px', borderRadius: '50px' }}/>
                            </div>
                            <h5 className="mb-3">Withdraw Money</h5>
                            <p>Withdrawing money allows customers to quickly access their funds whenever they need it for daily expenses, shopping, or emergencies.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.3s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                {/* <i className="fa fa-mouse-pointer fa-2x"></i> */}
                                <img src={`${appUrl}/website/assets/img/money-deposit.png`} style={{ height: '100px', borderRadius: '50px' }}/>
                            </div>
                            <h5 className="mb-3">Deposit service</h5>
                            <p>Your money is kept securely in a bank or authorized financial institution instead of keeping cash at home.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                    <div className="col-lg-4 col-md-6 wow zoomIn" data-wow-delay="0.6s">
                        <div className="service-item d-flex flex-column justify-content-center text-center rounded">
                            <div className="service-icon flex-shrink-0">
                                <img src={`${appUrl}/website/assets/img/epay.png`} style={{ height: '100px', borderRadius: '50px' }}/>
                            </div>
                            <h5 className="mb-3">E Pay</h5>
                            <p>E-Pay (Electronic Payment) is fast, convenient, and secure, making it one of the best ways to handle modern financial transactions.</p>
                            <a className="btn px-3 mt-auto mx-auto" href="">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/* <!-- Service End --> */}

         <div className="container-fluid py-5 bg-primary mb-5">
                <div className="container my-5 py-5 px-lg-5">
                    <div className="row g-5 py-5">
                        <div className="col-lg-6 text-center text-lg-start">
                            <h1 className="text-white mb-4 animated zoomIn">{t('Secure and fast currency exchange with the best daily rates')}</h1>
                            <p className="text-white pb-3 animated zoomIn">{t('Exchange your money with confidence. Our rates are updated daily based on the international market to ensure fair and transparent transactions')}</p>
                            <a href="" className="btn btn-light py-sm-3 px-sm-5 rounded-pill me-3 animated slideInLeft">Free Quote</a>
                            <a href="" className="btn btn-outline-light py-sm-3 px-sm-5 rounded-pill animated slideInRight">Contact Us</a>
                        </div>
                        <div className="col-lg-6 text-center text-lg-start">
                            <img className="img-fluid" src={`${appUrl}/website/assets/img/home.gif`}/>
                        </div>  
                    </div>
                </div>
            </div>

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
       
    </>
  );
}