<nav id="navigation">
    <div class="container">
        <div id="responsive-nav">
            <ul class="main-nav nav navbar-nav">
                <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">Trang chủ</a>
                </li>

                @foreach ($globalCategories as $category)
                    @php
                        $hasSub = $category->subCategories->where('status', 1)->count() > 0;
                        $subCategoryMatch = $category->subCategories->firstWhere('slug', request()->route('slug'));
                        $isActive = false;

                        if (!$hasSub) {
                            $isActive =
                                request()->routeIs('category.show') && request()->route('slug') == $category->slug;
                        } else {
                            $isActive =
                                request()->routeIs('subcategory.show') &&
                                $subCategoryMatch &&
                                $subCategoryMatch->slug == request()->route('slug');
                        }
                    @endphp
                    <li class="{{ $hasSub ? 'dropdown' : '' }} {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('category.show', $category->slug) }}"
                            class="{{ $hasSub ? 'dropdown-toggle' : '' }}" {{ $hasSub ? 'data-toggle=dropdown' : '' }}>
                            {{ $category->name }}
                            @if ($hasSub)
                                <span class="caret"></span>
                            @endif
                        </a>

                        @if ($hasSub)
                            <ul class="dropdown-menu">
                                @foreach ($category->subCategories as $subCategory)
                                    <li>
                                        <a href="{{ route('subcategory.show', $subCategory->slug) }}">
                                            {{ $subCategory->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach

                @foreach ($globalPages as $page)
                    <li
                        class="{{ request()->routeIs('pages.show') && request()->route('slug') === $page->slug ? 'active' : '' }}">
                        <a href="{{ route('pages.show', $page->slug) }}">{{ $page->title }}</a>
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
</nav>
