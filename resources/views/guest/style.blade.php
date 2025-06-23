<style>
    .bg-custom-green {
        background: linear-gradient(to right, #0088ff, #4ac0f6);
    }

    .navbar-search {
        flex: 1;
        max-width: 700px;
        position: relative;
    }

    .navbar-search .form-control {
        width: 100%;
        border-radius: 50px;
        transition: all 0.3s ease;
        padding-left: 40px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .navbar-search .form-control::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .navbar-search .form-control:focus {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        background-color: white;
        color: #333;
        border-color: white;
    }

    .navbar-search .form-control:focus::placeholder {
        color: #999;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 10px;
        color: rgba(255, 255, 255, 0.8);
        z-index: 10;
        transition: all 0.3s ease;
    }

    .navbar-search .form-control:focus+.search-icon {
        color: #0088ff;
    }

    #search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-top: none;
        border-radius: 0 0 15px 15px;
        display: none;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    #search-suggestions .suggestion-item {
        padding: 10px 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f0f0f0;
    }

    #search-suggestions .suggestion-item:last-child {
        border-bottom: none;
    }

    #search-suggestions .suggestion-item:hover {
        background: #fff8f0;
    }

    #search-suggestions .suggestion-item img {
        width: 40px;
        height: 40px;
        margin-right: 10px;
        object-fit: cover;
        border-radius: 5px;
    }

    #search-suggestions .suggestion-item .suggestion-details {
        flex: 1;
    }

    #search-suggestions .suggestion-item .suggestion-name {
        font-weight: 600;
        color: #333;
    }

    #search-suggestions .suggestion-item .suggestion-category {
        font-size: 12px;
        color: #6c757d;
    }

    #search-suggestions .suggestion-item .suggestion-price {
        font-weight: 600;
        color: #0095ff;
    }

    .feature-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .header-icon {
        font-size: 1.5rem;
        color: white;
        margin-right: 20px;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }

    .header-icon:hover {
        transform: scale(1.1);
    }

    .header-icon .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        font-size: 10px;
        padding: 3px 6px;
        border-radius: 50%;
        background-color: #0062ff;
        color: white;
        border: 2px solid #006eff;
    }

    .user-profile {
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .user-profile:hover {
        transform: scale(1.05);
    }

    .user-profile img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
    }

    .user-profile:hover img {
        border-color: white;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    .hero-section {
        padding: 60px 0;
        background: linear-gradient(to right, #0088ff, #52a4fc);
        color: white;
        margin-bottom: 30px;
    }

    .dropdown-menu {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.3s ease;
    }

    .dropdown-item {
        padding: 10px 20px;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #fff8f0;
        color: #0088ff;
    }

    .dropdown-item i {
        margin-right: 10px;
        color: #0088ff;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .navbar-search {
            max-width: 100%;
            margin: 10px 0;
        }

        .header-icon {
            margin-right: 15px;
        }

        .user-profile span {
            display: none;
        }
    }

    /* Cart tooltip */
    .tooltip-cart {
        position: absolute;
        top: 100%;
        right: -80px;
        width: 300px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        padding: 15px;
        display: none;
        z-index: 1000;
    }

    .tooltip-cart h6 {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .tooltip-cart .cart-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f5f5f5;
    }

    .tooltip-cart .cart-item img {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        margin-right: 10px;
    }

    .tooltip-cart .cart-item-details {
        flex: 1;
    }

    .tooltip-cart .cart-item-name {
        font-weight: 600;
        margin-bottom: 0;
    }

    .tooltip-cart .cart-item-price {
        color: #0088ff;
        font-weight: 600;
    }

    .tooltip-cart .cart-item-quantity {
        font-size: 12px;
        color: #6c757d;
    }

    .tooltip-cart .cart-total {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }

    .tooltip-cart .btn {
        margin-top: 10px;
        background-color: #0088ff;
        border-color: #0088ff;
    }

    .tooltip-cart .btn:hover {
        background-color: #0088ff;
        border-color: #0088ff;
    }

    /* History tooltip */
    .tooltip-history {
        position: absolute;
        top: 100%;
        right: -80px;
        width: 300px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        padding: 15px;
        display: none;
        z-index: 1000;
    }

    .tooltip-history h6 {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .tooltip-history .history-item {
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .tooltip-history .history-item:last-child {
        border-bottom: none;
    }

    .tooltip-history .history-date {
        font-size: 12px;
        color: #6c757d;
    }

    .tooltip-history .history-details {
        display: flex;
        justify-content: space-between;
    }

    .tooltip-history .history-id {
        font-weight: 600;
    }

    .tooltip-history .history-amount {
        color: #0088ff;
        font-weight: 600;
    }

    .tooltip-history .btn {
        margin-top: 10px;
        border-color: #0088ff;
        color: #0088ff;
    }

    .tooltip-history .btn:hover {
        background-color: #0088ff;
        color: white;
    }

    /* Navbar toggle button */
    .navbar-toggler {
        border: none;
        background: transparent;
    }

    .navbar-toggler:focus {
        outline: none;
        box-shadow: none;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Feature icons */
    .feature-icon {
        color: #0088ff;
    }

    /* Button styling */
    .btn-success {
        background-color: #0088ff;
        border-color: #0088ff;
    }

    .btn-success:hover {
        background-color: #0088ff;
        border-color: #0088ff;
    }

    .btn-outline-success {
        color: #0088ff;
        border-color: #0088ff;
    }

    .btn-outline-success:hover {
        background-color: #0088ff;
        color: white;
    }



    /* Carousel styling */
    #hero-carousel {
        margin-bottom: 30px;
    }

    #hero-carousel .carousel-item {
        min-height: 400px;
    }

    #hero-carousel .carousel-indicators {
        bottom: 20px;
    }

    #hero-carousel .carousel-indicators li {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin: 0 5px;
        background-color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #hero-carousel .carousel-indicators li.active {
        background-color: white;
        transform: scale(1.2);
    }

    #hero-carousel .carousel-control-prev,
    #hero-carousel .carousel-control-next {
        width: 50px;
        height: 50px;
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.7;
    }

    #hero-carousel .carousel-control-prev {
        left: 20px;
    }

    #hero-carousel .carousel-control-next {
        right: 20px;
    }

    #hero-carousel .carousel-control-prev:hover,
    #hero-carousel .carousel-control-next:hover {
        opacity: 1;
        background-color: rgba(255, 255, 255, 0.5);
    }

    #hero-carousel h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    #hero-carousel .lead {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
    }

    #hero-carousel .btn-outline-light:hover {
        background-color: white;
        color: #00bbff;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #hero-carousel .carousel-item {
            min-height: 500px;
            text-align: center;
        }

        #hero-carousel .col-md-6 {
            margin-bottom: 2rem;
        }

        #hero-carousel h1 {
            font-size: 2rem;
        }

        #hero-carousel .lead {
            font-size: 1.1rem;
        }

        #hero-carousel .d-flex {
            flex-direction: column;
            align-items: center;
        }

        #hero-carousel .btn {
            margin: 0.5rem;
        }
    }

    .text-overlay {
        background: rgba(0, 0, 0, 0.5);
        /* Latar belakang hitam dengan opacity */
        border-radius: 0px  0px  8px 8px;
        /* Opsional, untuk sudut yang lebih halus */
    }
</style>
