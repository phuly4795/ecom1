<nav id="navigation">
    <div class="container">
        <div id="responsive-nav">
            <ul class="main-nav nav navbar-nav">
                @foreach ($globalCategories as $category)
                    <li
                        class="{{ request()->routeIs('category.show') && request()->route('slug') == $category->slug ? 'active' : '' }}">
                        <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>
