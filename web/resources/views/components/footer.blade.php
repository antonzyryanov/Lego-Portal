<footer class="site-footer">
    <div class="container site-footer__inner">
        <div>
            <p class="site-footer__brand">Lego Portal</p>
            <p class="site-footer__copy">
                Fan-built catalog of classic themes — sets, news, and brick talk.
                Not affiliated with the LEGO Group.
            </p>
        </div>
        <ul class="site-footer__nav">
            <li><a href="{{ route('sets.index') }}">Sets</a></li>
            <li><a href="{{ route('news.index') }}">News</a></li>
            <li><a href="{{ route('forum.index') }}">Forum</a></li>
            @guest
                <li><a href="{{ route('login') }}">Sign In</a></li>
            @endguest
        </ul>
    </div>
</footer>
