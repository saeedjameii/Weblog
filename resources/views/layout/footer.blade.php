    <footer class="footer site-footer-glass">
        <div class="container">
            <div class="footer-cols">
                <div>
                    <strong>پایگاه خبری چی بهتره؟</strong>
                    <p>دقیق، سریع، بی‌طرف</p>
                </div>
                <nav aria-label="اخبار">
                    <h2>اخبار</h2>
                    <ul>
                        <li><a href="{{ route('posts.index') }}">تازه‌ترین اخبار</a></li>
                        <li><a href="{{ route('categories.posts.index') }}">دسته‌بندی‌های خبری</a></li>
                    </ul>
                </nav>
                <nav aria-label="فروشگاه">
                    <h2>فروشگاه</h2>
                    <ul>
                        <li><a href="{{ route('products.index') }}">همه‌ی محصولات</a></li>
                        <li><a href="{{ route('categories.products.index') }}">دسته‌بندی محصولات</a></li>
                    </ul>
                </nav>
            </div>
            <div class="footer-note">چی بهتره؟</div>
        </div>
    </footer>
    @stack('scripts')
    </body>

    </html>
