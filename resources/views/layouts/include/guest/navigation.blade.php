<nav id="navigation">
    <div class="container">
        <div id="responsive-nav">
            <ul class="main-nav nav navbar-nav">
                @foreach ($globalCategories as $category)
                    @php
                        $hasSub = $category->subCategories->count() > 0;
                        $isActive =
                            request()->routeIs('subcategory.show') && request()->route('slug') == $category->slug;
                    @endphp
                    <li class="{{ $hasSub ? 'dropdown' : '' }} {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route('category.show', $category->slug) }}"
                            class="{{ $hasSub ? 'dropdown-toggle' : '' }}" {{ $hasSub ? 'data-toggle=dropdown' : '' }}
                            onclick="if(event.target.tagName === 'SPAN') event.preventDefault();">
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
            </ul>
        </div>
    </div>
</nav>
