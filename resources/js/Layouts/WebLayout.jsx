import { Link, Head, usePage } from '@inertiajs/react';


export default function WebLayout({ children }) {

    // For language setup START
    const { translations, locale, ziggy } = usePage().props;
    const appUrl = window.location.origin;
    const t = (key) => translations[key] ?? key;
    const isActive = (name) => route().current(name);
    const languages = {
        en: {
            name: "English",
            flag: "/website/assets/flags/en.png"
        },
        "th-TH": {
            name: "Thai",
            flag: "/website/assets/flags/th.png"
        },
        km: {
            name: "Khmer",
            flag: "/website/assets/flags/kh.png"
        }
    };
    const currentLang = languages[locale];
    // console.log(locale)
    // console.log(translations)
     // For language setup END

  return (
    <>
    {/* <Head>
      <meta head-key="description" name="description" content="This is the default description" />
      <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    </Head>
      <header>
        <nav>
            <Link href="/">Home</Link>
            <Link href="/moneyexchange">moneyexchange</Link>
        </nav>
      </header> */}
      <Head>
        <meta head-key="description" name="description" content={t('Exchange currency rates help customers convert money correctly, make secure payments, and ensure transparency and trust in international transactions')}/>
        <link rel="icon" type="image/svg+xml" href={`${appUrl}/website/assets/logo/logo.png`}/>
    </Head>
    {/* <!-- Navbar & Hero Start --> */}
        <div className="container-fluid position-relative p-0">
            <nav className={`navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0 ${isActive('home') ? '' : 'bg-primary'}`}>
                <Link href="/" className="navbar-brand p-0">
                    {/* <h1 className="m-0">Money<span className="fs-5">Exchange AI</span></h1> */}
                    {/* <!-- <img src="img/logo.png" alt="Logo"> --> */}
                    <img src={`${appUrl}/website/assets/logo/logo2.png`} style={{ borderRadius: '10px' }}/>
                </Link>
                <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span className="fa fa-bars"></span>
                </button>
                <div className="collapse navbar-collapse" id="navbarCollapse">
                    <div className="navbar-nav ms-auto py-0">
                        <Link href={route('home')} className={`nav-item nav-link ${isActive('home') ? 'active' : ''}`}>{t('Home')}</Link>
                        <Link href="register" className={`nav-item nav-link`}>{t('Staff Login')}</Link>
                        <Link href={route('showExchangeRate')} className={`nav-item nav-link ${ isActive('showExchangeRate') ? 'active' : '' }`}>{t('Today Exchange Rate')}</Link>
                        <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${ isActive('open.moneyexchange.*') ? 'active' : '' }`}>{t('Money Exchange')}</Link>
                        {/* <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${ isActive('open.moneyexchange.*') ? 'active' : '' }`}>{t('Administrator Login')}</Link> */}
                        <div className="nav-item dropdown">
                            <a href="#" className="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img src={currentLang.flag} width="20" style={{marginRight:'6px'}}/>
                                {t(currentLang.name)}
                            </a>

                            <div className="dropdown-menu m-0">
                                <a href="/lang/en" className="dropdown-item">
                                    <img src="/website/assets/flags/en.png" width="20" className="me-2"/>{t('English')}
                                </a>
                                <a href="/lang/th-TH" className="dropdown-item">
                                    <img src="/website/assets/flags/th.png" width="20" className="me-2"/>{t('Thai')}
                                </a>
                                <a href="/lang/km" className="dropdown-item">
                                    <img src="/website/assets/flags/kH.png" width="20" className="me-2"/>{t('Khmer')}
                                </a>
                            </div>
                        </div>

                    </div>
                    {/* <button type="button" className="btn text-secondary ms-3"><i className="fa fa-search"></i></button> */}
                </div>
            </nav>

            {/* <div className="container-fluid py-5 mb-5">
                <div className="container my-5 py-5 px-lg-5">
                    <div className="row g-5 py-5">

                            {isActive('showExchangeRate') && (
                                                                <div className="col-12 text-center">
                                                                    <h1 className="text-white animated zoomIn">
                                                                        {t('Today Exchange')}
                                                                    </h1>
                                                                </div>
                                                            )}
                            {isActive('open.moneyexchange.form') && (
                                                                <div className="col-12 text-center">
                                                                    <h1 className="text-white animated zoomIn">
                                                                        {t('International Money Exchange')}
                                                                    </h1>
                                                                </div>
                                                            )}



                            
                        
                    </div>
                </div>
            </div> */}
        </div>
        {/* <!-- Navbar & Hero End --> */}

      <main>
        {children}
      </main>

        {/* <!-- Footer Start --> */}
        <div className="container-fluid py-3 bg-primary text-light footer wow fadeIn" data-wow-delay="0.1s">
            <div className="container py-1 px-lg-5">
                <div className="row g-5">
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">{t('Quick Contacts')}</h5>
                        <p><i className="fa fa-map-marker-alt me-3"></i>Beer City Poipet Zone 3</p>
                        <p><i className="fa fa-phone-alt me-3"></i>(+855) 973294524</p>
                        <p><i className="fa fa-envelope me-3"></i>beercity@gmail.com</p>
                        <div className="d-flex">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-facebook-f"></i></a>
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-youtube"></i></a>
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-instagram"></i></a>
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-telegram"></i></a>
                        </div>
                    </div>
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">{t('Market Fluctuations')}</h5>
                        <Link href={route('showExchangeRate')} className="btn btn-link">{t('Today Exchange Rate')}</Link>
                        <Link href={route('open.moneyexchange.form')} className="btn btn-link">{t('Money Transfer')}</Link>
                        <Link href={route('open.moneyexchange.form')} className="btn btn-link">{t('Administrator Login')}</Link>
                        <a className="btn btn-link" href="#">{t('Contact with Grand Diamond City')}</a>
                    </div>
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">{t('Faster International Payments')}</h5>
                        <div className="row g-2">
                            <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-1.jpg`} alt=""/></div>
                            <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-2.jpg`} alt=""/></div>
                            <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-3.jpg`} alt=""/></div>
                            <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-4.jpg`} alt=""/></div>
                            <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-5.jpg`} alt=""/></div>
                            <div className="col-4"><img className="img-fluid" src={`${appUrl}/website/assets/img/portfolio-6.jpg`} alt=""/></div>
                        </div>
                    </div>
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">{t('Why_People_Like_us')}</h5>
                        <p>{t('Exchange currency rates help customers convert money correctly, make secure payments, and ensure transparency and trust in international transactions')}</p>
                        <div className="position-relative w-100 mt-3">
                            <input className="form-control border-0 rounded-pill w-100 ps-4 pe-5" type="text" placeholder={t('Enter email')} style={{ height: '48px' }}/>
                            <button type="button" className="btn shadow-none position-absolute top-0 end-0 mt-1 me-2">
                                <i className="fa fa-paper-plane text-primary fs-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div className="container px-lg-5">
                <div className="copyright">
                    <div className="row">
                        <div className="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            {t('Copyright')} &copy; 2025-2026 {t('by')} <a className="border-bottom" href="https://zaffrantech.com/">Zaffran Tech</a> {t('All_Rights_Reserved')}
                        </div>
                        <div className="col-md-6 text-center text-md-end">
                            <div className="footer-menu">
                                <Link href={route('home')}>{t('Home')}</Link>
                                <a href="#">{t('Cookies')}</a>
                                <a href="#">{t('Help')}</a>
                                <a href="#">{t('FQAs')}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/* <!-- Footer End --> */}


        {/* <!-- Back to Top --> */}
        <a href="#" className="btn btn-lg btn-primary btn-lg-square back-to-top pt-2"><i className="bi bi-arrow-up"></i></a>
      
    </>
  );
}