import { Head, usePage } from '@inertiajs/react';
   

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
       
    </>
  );
}