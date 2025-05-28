<nav id="navigation">
    <div class="container">
        <div id="responsive-nav">
            <ul class="main-nav nav navbar-nav">
                @foreach ($globalCategories as $category)
                    <li
                        class="dropdown {{ request()->routeIs('subcategory.show') && request()->route('slug') == $category->slug ? 'active' : '' }}">
                        <!-- Link đến danh mục cha + caret tách riêng -->
                        <a href="{{ route('category.show', $category->slug) }}" class="dropdown-toggle"
                            data-toggle="dropdown" onclick="if(event.target.tagName === 'SPAN') event.preventDefault();">
                            {{ $category->name }}
                            @if ($category->subCategories->count() > 0)
                                <span class="caret"></span>
                            @endif
                        </a>

                        <!-- Menu thả xuống cho danh mục phụ -->
                        @if ($category->subCategories->count() > 0)
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
