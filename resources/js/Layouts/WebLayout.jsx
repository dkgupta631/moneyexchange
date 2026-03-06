import { Link, usePage } from '@inertiajs/react';


export default function WebLayout({ children }) {

    // For language setup START
    const { translations, locale, ziggy } = usePage().props;
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
    {/* <!-- Navbar & Hero Start --> */}
        <div className="container-fluid position-relative p-0">
            <nav className="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <Link href="/" className="navbar-brand p-0">
                    <h1 className="m-0">Money<span className="fs-5">Exchange AI</span></h1>
                    {/* <!-- <img src="img/logo.png" alt="Logo"> --> */}
                </Link>
                <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span className="fa fa-bars"></span>
                </button>
                <div className="collapse navbar-collapse" id="navbarCollapse">
                    <div className="navbar-nav ms-auto py-0">
                        <Link href={route('home')} className={`nav-item nav-link ${isActive('home') ? 'active' : ''}`}>Home</Link>
                        <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${ isActive('open.moneyexchange.*') ? 'active' : '' }`}>Register</Link>
                        <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${ isActive('open.moneyexchange.*') ? 'active' : '' }`}>Administrator Login</Link>
                        <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${ isActive('open.moneyexchange.*') ? 'active' : '' }`}>Today Exchange Rate</Link>
                        <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${ isActive('open.moneyexchange.*') ? 'active' : '' }`}>Money Transfer</Link>
                        <Link href={route('open.moneyexchange.form')} className={`nav-item nav-link ${ isActive('open.moneyexchange.*') ? 'active' : '' }`}>Language</Link>
                        <div className="nav-item dropdown">
                            <a href="#" className="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img
                                    src={currentLang.flag}
                                    width="20"
                                    style={{marginRight:'6px'}}
                                />
                                {currentLang.name}
                            </a>

                            <div className="dropdown-menu m-0">

                                <a href="/lang/en" className="dropdown-item">
                                    <img src="/website/assets/flags/en.png" width="20" className="me-2"/>
                                    English
                                </a>

                                <a href="/lang/th-TH" className="dropdown-item">
                                    <img src="/website/assets/flags/th.png" width="20" className="me-2"/>
                                    Thai
                                </a>

                                <a href="/lang/km" className="dropdown-item">
                                    <img src="/website/assets/flags/kH.png" width="20" className="me-2"/>
                                    Khmer
                                </a>

                            </div>
                        </div>

                    </div>
                    {/* <button type="button" className="btn text-secondary ms-3"><i className="fa fa-search"></i></button> */}
                </div>
            </nav>

            <div className="container-fluid py-5 bg-primary hero-header mb-5">
                <div className="container my-5 py-5 px-lg-5">
                    <div className="row g-5 py-5">
                        <div className="col-12 text-center">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/* <!-- Navbar & Hero End --> */}

      <main>
        {children}
      </main>

        {/* <!-- Footer Start --> */}
        {/* <div className="container-fluid bg-primary text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
            <div className="container py-5 px-lg-5">
                <div className="row g-5">
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">Get In Touch</h5>
                        <p><i className="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA</p>
                        <p><i className="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                        <p><i className="fa fa-envelope me-3"></i>info@example.com</p>
                        <div className="d-flex pt-2">
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-twitter"></i></a>
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-facebook-f"></i></a>
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-youtube"></i></a>
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-instagram"></i></a>
                            <a className="btn btn-outline-light btn-social" href="#"><i className="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">Popular Link</h5>
                        <a className="btn btn-link" href="#">About Us</a>
                        <a className="btn btn-link" href="#">Contact Us</a>
                        <a className="btn btn-link" href="#">Privacy Policy</a>
                        <a className="btn btn-link" href="#">Terms & Condition</a>
                        <a className="btn btn-link" href="#">Career</a>
                    </div>
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">Project Gallery</h5>
                        <div className="row g-2">
                            <div className="col-4"></div>
                            <div className="col-4"></div>
                            <div className="col-4"></div>
                            <div className="col-4"></div>
                            <div className="col-4"></div>
                            <div className="col-4"></div>
                        </div>
                    </div>
                    <div className="col-md-6 col-lg-3">
                        <h5 className="text-white mb-4">Newsletter</h5>
                        <p>Lorem ipsum dolor sit amet elit. Phasellus nec pretium mi. Curabitur facilisis ornare velit non vulpu</p>
                        <div className="position-relative w-100 mt-3">
                            <input className="form-control border-0 rounded-pill w-100 ps-4 pe-5" type="text" placeholder="Your Email" style={{ height: '48px' }}/>
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
                            © All Right Reserved. Designed By <a className="border-bottom" href="https://zaffrantech.com/">Zaffran Tech</a>
                        </div>
                        <div className="col-md-6 text-center text-md-end">
                            <div className="footer-menu">
                                <a href="#">Home</a>
                                <a href="#">Cookies</a>
                                <a href="#">Help</a>
                                <a href="#">FQAs</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> */}
        {/* <!-- Footer End --> */}


        {/* <!-- Back to Top --> */}
        <a href="#" className="btn btn-lg btn-primary btn-lg-square back-to-top pt-2"><i className="bi bi-arrow-up"></i></a>
      
    </>
  );
}