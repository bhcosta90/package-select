{{--
    Usage:
    - Livewire + URL:     wire:model="prop" url="/api/..."
    - Blade + URL:        name="field" url="/api/..."
    - Livewire + Options: wire:model="prop" :options="[1=>'A', 2=>'B']"
    - Blade + Options:    name="field" :options="[1=>'A', 2=>'B']" :value="$val"
    - Multiple:           add the "multiple" attribute
    - Optgroup:           :options="['Group' => ['k' => 'Label', ...], ...]"
--}}
@props([
    'url'             => '',
    'valueKey'        => 'id',
    'labelKey'        => 'name',
    'multiple'        => false,
    'name'            => '',
    'value'           => null,
    'values'          => [],
    'params'          => [],
    'options'         => [],  // array, Enum::class, or Enum::cases()
    'label'           => '',
    'placeholder'     => '',
    'required'        => false,
    'minSearchLength' => 2,
    'inputBorder'     => '',
    'inputFocusBorder'=> '',
    'inputFocusRing'  => '',
    'dropdownBorder'  => '',
    'itemHoverBg'     => '',
    'itemHoverText'   => '',
    'itemSelectedBg'  => '',
    'itemSelectedText'=> '',
    'itemSelectedIcon'=> '',
    'placeholderText' => '',
    'tagBg'           => '',
    'tagText'         => '',
    'tagHoverText'    => '',
    'footerBorder'    => '',
    'footerBg'        => '',
    'footerText'      => '',
    'emptyBorder'     => '',
    'emptyBg'         => '',
    'emptyText'       => '',
])
@php
    // Apply theme defaults when attribute was not explicitly passed
    $theme = app(\Brcas\Select\SelectTheme::class);
    $inputWrapperBase = $theme->get('input_wrapper_base');
    $inputBorder      = $inputBorder ?: $theme->get('input_border');
    $inputFocusBorder = $inputFocusBorder ?: $theme->get('input_focus_border');
    $inputFocusRing   = $inputFocusRing ?: $theme->get('input_focus_ring');
    $dropdownBorder   = $dropdownBorder ?: $theme->get('dropdown_border');
    $itemHoverBg      = $itemHoverBg ?: $theme->get('item_hover_bg');
    $itemHoverText    = $itemHoverText ?: $theme->get('item_hover_text');
    $itemSelectedBg   = $itemSelectedBg ?: $theme->get('item_selected_bg');
    $itemSelectedText = $itemSelectedText ?: $theme->get('item_selected_text');
    $itemSelectedIcon = $itemSelectedIcon ?: $theme->get('item_selected_icon');
    $placeholderText  = $placeholderText ?: $theme->get('placeholder_text');
    $tagBg            = $tagBg ?: $theme->get('tag_bg');
    $tagText          = $tagText ?: $theme->get('tag_text');
    $tagHoverText     = $tagHoverText ?: $theme->get('tag_hover_text');
    $footerBorder     = $footerBorder ?: $theme->get('footer_border');
    $footerBg         = $footerBg ?: $theme->get('footer_bg');
    $footerText       = $footerText ?: $theme->get('footer_text');
    $emptyBorder      = $emptyBorder ?: $theme->get('empty_border');
    $emptyBg          = $emptyBg ?: $theme->get('empty_bg');
    $emptyText        = $emptyText ?: $theme->get('empty_text');

    // Normalize static options to [{value, label}, ...] with optgroup/enum support
    $normalizedOptions = [];

    // Support passing Enum class as string: :options="StatusEnum::class"
    if (is_string($options) && enum_exists($options)) {
        /** @var class-string<\UnitEnum> $enumClass */
        $enumClass = $options;
        $options = $enumClass::cases();
    }

    // Support passing Enum cases array: :options="StatusEnum::cases()"
    if (is_array($options) && !empty($options) && reset($options) instanceof \UnitEnum) {
        $enumOptions = [];
        foreach ($options as $case) {
            $value = $case instanceof \BackedEnum ? $case->value : $case->name;
            $label = method_exists($case, 'label') ? $case->label() : $case->name;
            $enumOptions[] = ['value' => $value, 'label' => $label];
        }
        $options = $enumOptions;
    }

    foreach ($options as $key => $val) {
        if (is_array($val) && !array_is_list($val) && !isset($val['value'])) {
            $normalizedOptions[] = ['_group' => true, 'label' => (string) $key];
            foreach ($val as $subKey => $subVal) {
                $normalizedOptions[] = ['value' => $subKey, 'label' => $subVal];
            }
        } elseif (is_array($val)) {
            $normalizedOptions[] = $val;
        } else {
            $normalizedOptions[] = ['value' => $key, 'label' => $val];
        }
    }

    $isLivewire = $attributes->whereStartsWith('wire:model')->isNotEmpty();
    $xParams    = $attributes->get('x-params');
    $isOptions  = count($normalizedOptions) > 0;
    $wireProp   = '';

    if ($isLivewire) {
        $wireAttr     = $attributes->whereStartsWith('wire:model')->first();
        $wireProp     = $wireAttr ?? '';
        $initialValue = ($wireProp && isset($__livewire))
            ? data_get($__livewire, $wireProp)
            : null;
    } else {
        $initialValue = $multiple ? array_values($values) : $value;
    }

    $focusRef = $attributes->get('x-ref', '');

    $wireError = null;
    if ($isLivewire && $wireProp) {
        $sharedErrors = app('view')->getShared()['errors'] ?? null;
        if ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag) {
            $wireError = $sharedErrors->first($wireProp) ?: null;
        }
    }
@endphp

<div>
    @if ($label)
        <x-label :label="$label" :error="(bool) $wireError" />
    @endif
    <div
        class="relative w-full"
        data-url="{{ $url }}"
        data-value-key="{{ $valueKey }}"
        data-label-key="{{ $labelKey }}"
        data-multiple="{{ $multiple ? 'true' : 'false' }}"
        data-params="{{ json_encode($params) }}"
        data-initial="{{ json_encode($initialValue) }}"
        data-options="{{ $isOptions ? json_encode($normalizedOptions) : 'null' }}"
        data-placeholder="{{ $placeholder }}"
        data-required="{{ $required ? 'true' : 'false' }}"
        data-min-search-length="{{ $minSearchLength }}"
        data-i18n-loading="@lang('Loading')"
        data-i18n-search="@lang('Search')"
        data-i18n-footer="@lang('1–:showing of :total records')"
        data-item-hover-bg="{{ $itemHoverBg }}"
        data-item-hover-text="{{ $itemHoverText }}"
        data-item-selected-bg="{{ $itemSelectedBg }}"
        data-item-selected-text="{{ $itemSelectedText }}"
        data-item-selected-icon="{{ $itemSelectedIcon }}"
        data-placeholder-text="{{ $placeholderText }}"
        @if($xParams)
            x-effect="
            const _p = {{ $xParams }};
            if (JSON.stringify(_p) !== JSON.stringify(params)) {
                params = _p;
                results = []; page = 1; hasMorePages = false;
                modelValue = multiple ? [] : null;
                selectedLabel = ''; search = ''; selectedItems = [];
                open = false;
            }
        "
        @endif
        x-data="{

        {{-- ============================================================ --}}
        {{-- STATE                                                         --}}
        {{-- ============================================================ --}}

        open:            false,
        dropdownUp:      false,
        loading:         false,
        loadingMore:     false,
        opening:         false,
        initializing:    false,
        hasSearched:     false,

        search:          '',
        selectedLabel:   '',
        modelValue:      null,
        selectedItems:   [],
        results:         [],
        total:           null,
        page:            1,
        hasMorePages:    false,
        highlightedIndex: -1,

        dropdownStyle: {},

        // Settings read from DOM
        url:      '',
        valueKey: 'id',
        labelKey: 'name',
        multiple: false,
        params:   {},

        // Static options mode
        isOptionsMode: false,
        allOptions:    [],
        placeholder:   '',  // empty option text (e.g. 'Select...')
        required:      false,
        minSearchLength: 2,

        // Theme
        itemHoverBg:      '',
        itemHoverText:    '',
        itemSelectedBg:   '',
        itemSelectedText: '',
        itemSelectedIcon: '',
        placeholderText:  '',

        // Custom slot
        hasCustomSlot: false,
        slotTemplate:  null,

        // i18n
        i18nLoading: 'Loading...',
        i18nSearch:  'Search...',
        i18nFooter:  '1–:showing of :total records',

        // Internal controls
        selecting:      false,
        focused:        false,
        focusClearing:  false,
        _searchDebounce: null,
        _blurTimeout:    null,
        _isFocusing:     false,

        {{-- ============================================================ --}}
        {{-- INITIALIZATION                                                --}}
        {{-- ============================================================ --}}

        init() {
            // Read settings from data-* attributes
            this.url              = this.$el.dataset.url;
            this.valueKey         = this.$el.dataset.valueKey;
            this.labelKey         = this.$el.dataset.labelKey;
            this.multiple         = this.$el.dataset.multiple === 'true';
            this.itemHoverBg      = this.$el.dataset.itemHoverBg;
            this.itemHoverText    = this.$el.dataset.itemHoverText;
            this.itemSelectedBg   = this.$el.dataset.itemSelectedBg;
            this.itemSelectedText = this.$el.dataset.itemSelectedText;
            this.itemSelectedIcon = this.$el.dataset.itemSelectedIcon;
            this.placeholderText  = this.$el.dataset.placeholderText || '';
            this.placeholder      = this.$el.dataset.placeholder || '';
            this.required         = this.$el.dataset.required === 'true';
            this.minSearchLength  = parseInt(this.$el.dataset.minSearchLength || '2', 10);
            this.i18nLoading      = (this.$el.dataset.i18nLoading || 'Loading') + '...';
            this.i18nSearch       = (this.$el.dataset.i18nSearch || 'Search') + '...';
            this.i18nFooter       = this.$el.dataset.i18nFooter || '1–:showing of :total records';

            try { this.params = JSON.parse(this.$el.dataset.params || '{}'); }
            catch(e) { this.params = {}; }

            // Detect static options mode
            try {
                const optionsData = JSON.parse(this.$el.dataset.options || 'null');
                if (Array.isArray(optionsData)) {
                    this.isOptionsMode = true;
                    this.allOptions    = optionsData;
                    // Pre-load results silently (without opening dropdown)
                    this.filterOptions(false);
                }
            } catch(e) {}

            // Watch for external changes in data-params (e.g. via x-params)
            new MutationObserver(() => {
                try {
                    const newParams = JSON.parse(this.$el.dataset.params || '{}');
                    if (JSON.stringify(newParams) === JSON.stringify(this.params)) return;

                    this.params = newParams;
                    this.reset();
                } catch(e) {}
            }).observe(this.$el, { attributeFilter: ['data-params'] });

            // Capture custom slot template if present
            const tpl = this.$el.querySelector('[data-slot-template]');
            if (tpl) {
                this.hasCustomSlot = true;
                this.slotTemplate  = tpl.innerHTML.trim();
                tpl.remove();
            }

            // Pre-populate initial value (e.g. editing a record)
            this.$nextTick(async () => {
                try {
                    const raw = this.$el.dataset.initial;
                    if (!raw || raw === 'null') return;

                    const parsed     = JSON.parse(raw);
                    let initialIds   = [];

                    if (this.multiple) {
                        initialIds       = Array.isArray(parsed) ? parsed.filter(Boolean) : [];
                        this.modelValue  = [...initialIds];
                    } else if (parsed !== null && parsed !== undefined && parsed !== '') {
                        initialIds       = [parsed];
                        this.modelValue  = parsed;
                    }

                    if (initialIds.length > 0) {
                        if (this.isOptionsMode) {
                            this.initFromOptions(initialIds);
                        } else {
                            this.initializing = true;
                            await this.fetchInitial(initialIds);
                            this.initializing = false;
                        }
                    }
                } catch (e) {}
            });

            // Sync selectedItems / selectedLabel when modelValue is changed externally (e.g. Livewire reset)
            this.$watch('modelValue', (newVal) => {
                if (this.selecting || this.initializing) return;

                if (this.multiple) {
                    const isEmpty = !newVal || (Array.isArray(newVal) && newVal.length === 0);
                    if (isEmpty) {
                        this.selectedItems = [];
                    } else if (Array.isArray(newVal)) {
                        const newValStrings = newVal.map(v => String(v));
                        this.selectedItems = this.selectedItems.filter(s => newValStrings.includes(String(s.value)));
                    }
                } else {
                    if (newVal === null || newVal === undefined || newVal === '') {
                        this.selectedLabel = '';
                        this.search        = '';
                    }
                }
            });

            // Trigger search on typing (with debounce)
            this.$watch('search', () => {
                if (this.selecting || this.focusClearing || !this.focused) return;

                // If user is typing but hasn't reached min length yet, skip
                if (this.search.length > 0 && this.search.length < this.minSearchLength) return;

                clearTimeout(this._searchDebounce);
                this._searchDebounce = setTimeout(() => {
                    if (this.selecting || this.focusClearing || !this.focused) return;

                    if (!this.multiple) {
                        this.selectedLabel = '';
                        this.modelValue    = null;
                    }

                    this.results      = [];
                    this.page         = 1;
                    this.hasMorePages = false;
                    this.open         = false;
                    this.opening      = true;
                    this.computeDropdownDirection();

                    if (this.isOptionsMode) {
                        this.filterOptions();
                    } else {
                        this.fetchResults(true);
                    }
                }, 150);
            });
        },

        {{-- ============================================================ --}}
        {{-- STATIC OPTIONS MODE                                           --}}
        {{-- ============================================================ --}}

        // Populate selectedItems / selectedLabel from static options
        initFromOptions(ids) {
            const strIds = ids.map(id => String(id));
            const found  = this.allOptions.filter(o => !o._group && strIds.includes(String(o.value)));

            if (this.multiple) {
                this.selectedItems = found.map(o => ({ value: o.value, label: o.label }));
                this.modelValue    = this.selectedItems.map(s => s.value);
            } else if (found.length > 0) {
                // selecting=true prevents $watch('search') from opening the dropdown
                this.selecting     = true;
                this.selectedLabel = found[0].label;
                this.search        = found[0].label;
                this.$nextTick(() => { this.selecting = false; });
            }
            // Pre-load results without opening the dropdown
            this.filterOptions(false);
        },

        // Filter static options by search text
        // openDropdown=true only when called by user interaction (focus/typing)
        filterOptions(openDropdown = true) {
            const term = this.search.trim().toLowerCase();
            var filtered = [];

            if (!term) {
                filtered = this.allOptions.slice();
            } else {
                // Filter keeping group header if at least one child matches
                var pendingGroup = null;
                for (var i = 0; i < this.allOptions.length; i++) {
                    var o = this.allOptions[i];
                    if (o._group) {
                        pendingGroup = o;
                    } else if (String(o.label).toLowerCase().indexOf(term) !== -1) {
                        if (pendingGroup) { filtered.push(pendingGroup); pendingGroup = null; }
                        filtered.push(o);
                    }
                }
            }

            // Insert empty option at top (single only, no text filter active)
            if (this.placeholder && !this.multiple && !term) {
                filtered = [{ value: '', label: this.placeholder, _placeholder: true }, ...filtered];
            }

            this.results      = filtered;
            this.hasMorePages = false;
            this.total        = filtered.length;
            this.loading      = false;
            this.opening      = false;

            if (openDropdown) {
                this.hasSearched = true;
                this.open = this.results.length > 0;
                this.$nextTick(() => {
                    if (this.$refs.dropdown) this.$refs.dropdown.scrollTop = 0;
                });
            } else {
                // Silent: pre-loads results without opening or marking hasSearched
                this.hasSearched = false;
                this.open = false;
            }
        },

        {{-- ============================================================ --}}
        {{-- DROPDOWN DIRECTION (up or down)                               --}}
        {{-- ============================================================ --}}

        computeDropdownDirection() {
            const maxHeight = 240;
            const wrapper   = this.$el.querySelector('div[class*=\'flex min-h\']') ?? this.$el.firstElementChild;
            const el        = wrapper ?? this.$el;
            const rect      = el.getBoundingClientRect();

            if (rect.width === 0 || rect.left >= window.innerWidth || rect.right <= 0) {
                requestAnimationFrame(() => this.computeDropdownDirection());
                return;
            }

            const spaceBelow = window.innerHeight - rect.bottom;
            this.dropdownUp  = spaceBelow < maxHeight;

            const base = { width: rect.width + 'px', left: rect.left + 'px', maxHeight: maxHeight + 'px' };
            if (this.dropdownUp) {
                this.dropdownStyle = { ...base, bottom: (window.innerHeight - rect.top + 4) + 'px', top: 'auto' };
            } else {
                this.dropdownStyle = { ...base, top: (rect.bottom + 4) + 'px', bottom: 'auto' };
            }

            // Keep tracking while the parent container is still animating
            if (this.open || this.opening) {
                const snapLeft = rect.left;
                requestAnimationFrame(() => {
                    if (el.getBoundingClientRect().left !== snapLeft) {
                        this.computeDropdownDirection();
                    }
                });
            }
        },

        {{-- ============================================================ --}}
        {{-- SEARCH / FETCH                                                --}}
        {{-- ============================================================ --}}

        buildQuery(page) {
            return new URLSearchParams({ search: this.search, page, ...this.params }).toString();
        },

        // Fetch pre-selected items by ID (to populate initial value)
        async fetchInitial(ids) {
            try {
                const qs       = ids.map(function(id) { return 'ids[]=' + encodeURIComponent(id); }).join('&');
                const response = await fetch(this.url + '?' + qs);
                const json     = await response.json();
                const items    = json.data || [];

                if (this.multiple) {
                    this.selectedItems = items.map(item => ({
                        value: item[this.valueKey],
                        label: item[this.labelKey],
                    }));
                } else if (items.length > 0) {
                    this.selecting     = true;
                    this.selectedLabel = items[0][this.labelKey];
                    this.search        = items[0][this.labelKey];
                    this.$nextTick(() => { this.selecting = false; });
                }
            } catch (e) {}
        },

        // Main paginated search
        async fetchResults(openAfter = false) {
            this.loading      = true;
            this.page         = 1;
            this.hasMorePages = false;
            this.total        = null;
            this.hasSearched  = false;

            try {
                const response = await fetch(this.url + '?' + this.buildQuery(this.page));
                const json     = await response.json();
                this.results         = json.data || [];
                this.hasMorePages    = json.meta?.has_more_page ?? false;
                this.total           = json.meta?.total ?? null;
                this.highlightedIndex = -1;
            } catch (e) {
                this.results = [];
            }

            this.loading     = false;
            this.opening     = false;
            this.hasSearched = true;

            if (openAfter) {
                this.open = true;
                this.$nextTick(() => {
                    if (this.$refs.dropdown) this.$refs.dropdown.scrollTop = 0;
                });
            }
        },

        // Load more items on scroll to bottom
        async loadMore() {
            if (this.loadingMore || !this.hasMorePages) return;

            this.loadingMore = true;
            this.page++;

            try {
                const response = await fetch(this.url + '?' + this.buildQuery(this.page));
                const json     = await response.json();
                this.results      = [...this.results, ...(json.data || [])];
                this.hasMorePages = json.meta?.has_more_page ?? false;
                this.total        = json.meta?.total ?? null;
            } catch (e) {
                this.page--;
            }

            this.loadingMore = false;
        },

        {{-- ============================================================ --}}
        {{-- ITEM SELECTION                                                --}}
        {{-- ============================================================ --}}

        isSelected(item) {
            if (!this.multiple || item._group) return false;
            return this.selectedItems.some(s => s.value == item[this.isOptionsMode ? 'value' : this.valueKey]);
        },

        selectItem(item) {
            // Group header — ignore
            if (item._group) return;

            // Empty option (placeholder) — clear selection
            if (item._placeholder) {
                this.selecting     = true;
                this.modelValue    = null;
                this.selectedLabel = '';
                this.search        = '';
                this.open          = false;
                this.$nextTick(() => { this.selecting = false; });
                return;
            }

            const valKey = this.isOptionsMode ? 'value' : this.valueKey;
            const lblKey = this.isOptionsMode ? 'label' : this.labelKey;

            if (this.multiple) {
                const val = item[valKey];
                const idx = this.selectedItems.findIndex(s => s.value == val);

                if (idx >= 0) {
                    this.selectedItems.splice(idx, 1);
                } else {
                    this.selectedItems.push({ value: val, label: item[lblKey] });
                }

                this.modelValue = this.selectedItems.map(s => s.value);
                this.search     = '';

                if (this.isOptionsMode) {
                    // Keep dropdown open for multiple selection
                    this.$nextTick(() => {
                        this.filterOptions();
                        this.$el.querySelector('input[type=text]')?.focus();
                    });
                } else {
                    this.$nextTick(() => this.$el.querySelector('input[type=text]')?.focus());
                }
            } else {
                this.selecting     = true;
                this.modelValue    = item[valKey];
                this.selectedLabel = item[lblKey];
                this.search        = item[lblKey];
                this.open          = false;
                this.$nextTick(() => { this.selecting = false; });
            }
        },

        removeTag(value) {
            this.selectedItems = this.selectedItems.filter(s => s.value != value);
            this.modelValue    = this.selectedItems.map(s => s.value);
        },

        clear() {
            this.modelValue    = this.multiple ? [] : null;
            this.selectedLabel = '';
            this.search        = '';
            this.selectedItems = [];
            this.results       = [];
            this.page          = 1;
            this.hasMorePages  = false;
            this.open          = false;
            this.opening       = true;
            this.computeDropdownDirection();

            if (this.isOptionsMode) {
                this.filterOptions();
            } else {
                this.fetchResults(true);
            }
        },

        reset() {
            this.results      = [];
            this.page         = 1;
            this.hasMorePages = false;
            this.open         = false;

            if (this.multiple) {
                this.modelValue    = [];
                this.selectedItems = [];
            } else {
                this.modelValue    = null;
                this.selectedLabel = '';
                this.search        = '';
            }
        },

        {{-- ============================================================ --}}
        {{-- CUSTOM SLOT                                                   --}}
        {{-- ============================================================ --}}

        renderSlot(item) {
            if (!this.slotTemplate) return '';
            let html = this.slotTemplate;
            Object.keys(item).forEach(key => {
                const re = new RegExp('\\[:' + '\\s*' + key + '\\s*' + ':\\]', 'g');
                html = html.replace(re, item[key] ?? '');
            });
            return html;
        },

        {{-- ============================================================ --}}
        {{-- KEYBOARD AND FOCUS EVENTS                                     --}}
        {{-- ============================================================ --}}

        onScroll(event) {
            const el = event.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 80) {
                this.loadMore();
            }
        },

        onKeydown(event) {
            if (!this.open) return;

            const actions = {
                ArrowDown: () => {
                    event.preventDefault();
                    var next = this.highlightedIndex + 1;
                    while (next < this.results.length && this.results[next]._group) next++;
                    if (next < this.results.length) this.highlightedIndex = next;
                    this.scrollToHighlighted();
                    if (!this.isOptionsMode && this.highlightedIndex >= this.results.length - 3) {
                        this.loadMore().then(() => this.$nextTick(() => this.scrollToHighlighted()));
                    }
                },
                ArrowUp: () => {
                    event.preventDefault();
                    var prev = this.highlightedIndex - 1;
                    while (prev >= 0 && this.results[prev]._group) prev--;
                    if (prev >= 0) this.highlightedIndex = prev;
                    this.scrollToHighlighted();
                },
                Enter: () => {
                    event.preventDefault();
                    if (this.results[this.highlightedIndex]) {
                        this.selectItem(this.results[this.highlightedIndex]);
                    }
                },
                Escape: () => {
                    event.preventDefault();
                    this.open             = false;
                    this.highlightedIndex = -1;
                    if (!this.multiple && this.selectedLabel) {
                        this.selecting = true;
                        this.search    = this.selectedLabel;
                        this.$nextTick(() => { this.selecting = false; });
                    }
                },
            };

            actions[event.key]?.();
        },

        scrollToHighlighted() {
            const dd   = this.$refs.dropdown;
            const item = dd?.children[this.highlightedIndex];
            if (!dd || !item) return;

            const itemTop    = item.offsetTop;
            const itemBottom = itemTop + item.offsetHeight;

            if (itemTop < dd.scrollTop) {
                dd.scrollTop = itemTop;
            } else if (itemBottom > dd.scrollTop + dd.clientHeight) {
                dd.scrollTop = itemBottom - dd.clientHeight;
            }
        },

        onBlur() {
            this._blurTimeout = setTimeout(() => {
                if (this._isFocusing) return;

                if (!this.multiple && this.selectedLabel) {
                    this.selecting = true;
                    this.search    = this.selectedLabel;
                    this.$nextTick(() => { this.selecting = false; });
                }

                this.open    = false;
                this.opening = false;
                this.focused = false;
            }, 200);
        },

        onFocus(event) {
            this._isFocusing = true;
            clearTimeout(this._blurTimeout);
            clearTimeout(this._searchDebounce);
            this.$nextTick(() => { this._isFocusing = false; });

            this.focused = true;

            if (this.opening) return;

            if (!this.multiple && this.selectedLabel) {
                this.focusClearing = true;
                this.search = '';
                this.$nextTick(() => {
                    this.focusClearing = false;
                    this.results      = [];
                    this.page         = 1;
                    this.hasMorePages = false;
                    this.hasSearched  = false;
                    this.opening      = true;
                    this.computeDropdownDirection();

                    if (this.isOptionsMode) {
                        this.filterOptions();
                    } else {
                        this.fetchResults(true);
                    }
                });
            } else if (!this.open) {
                this.opening = true;
                this.computeDropdownDirection();

                if (this.isOptionsMode) {
                    this.filterOptions();
                } else {
                    this.fetchResults(true);
                }
            }

            event.target.select();
        },
    }"
        x-modelable="modelValue"
        @if($isLivewire)
            {{ $attributes->whereStartsWith('wire:model') }}
        @else
            {{ $attributes->whereStartsWith('x-model') }}
        @endif
        @click.window="if (open && !$el.contains($event.target) && !$event.target.closest('[data-brcas-dropdown]')) open = false"
>
    {{-- Custom slot template (hidden, used to render each item) --}}
    @if ($slot->isNotEmpty())
        <div data-slot-template style="display:none">{{ $slot }}</div>
    @endif

    {{-- Hidden inputs for Blade mode (form submit without Livewire) --}}
    @if (!$isLivewire && $name)
        @if (!$multiple)
            <input type="hidden" name="{{ $name }}" :value="modelValue ?? ''" />
        @else
            <template x-for="item in selectedItems" :key="item.value">
                <input type="hidden" name="{{ $name }}[]" :value="item.value" />
            </template>
            <template x-if="selectedItems.length === 0">
                <input type="hidden" name="{{ $name }}[]" value="" />
            </template>
        @endif
    @endif

    {{-- ================================================================ --}}
    {{-- INPUT                                                             --}}
    {{-- ================================================================ --}}
    <div
            class="flex min-h-[2.5rem] w-full flex-wrap items-center gap-1 rounded-md bg-white px-2 py-1 {{ $inputWrapperBase }} {{ $inputBorder }} {{ $inputFocusBorder }} {{ $inputFocusRing }}"
            :class="initializing ? 'opacity-60 cursor-not-allowed' : ''"
            @click="if (!initializing) $el.querySelector('input[type=text]').focus()"
    >
        {{-- Selected tags (multiple mode) --}}
        <template x-if="multiple">
            <template x-for="tag in selectedItems" :key="tag.value">
                <span
                        class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs font-medium cursor-pointer select-none {{ $tagBg }} {{ $tagText }} {{ $tagHoverText }}"
                        :class="initializing ? 'pointer-events-none opacity-60' : ''"
                        @click.stop="if (!initializing) removeTag(tag.value)"
                >
                    <span x-text="tag.label"></span>
                    <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </span>
            </template>
        </template>

        {{-- Search input --}}
        <input
                type="text"
                x-model="search"
                @focus="onFocus($event)"
                @click="onFocus($event)"
                @blur="onBlur()"
                @keydown="onKeydown($event)"
                :disabled="initializing"
                :placeholder="initializing ? i18nLoading : i18nSearch"
                @if($focusRef) data-focus="{{ $focusRef }}" @endif
                class="min-w-[120px] flex-1 border-none bg-transparent p-0.5 outline-none placeholder:text-gray-400 disabled:cursor-not-allowed {{ $theme->get('input_search_size') }}"
                autocomplete="off"
        />

        {{-- Spinner (loading / opening) --}}
        <template x-if="initializing || opening || (!initializing && !opening && loading)">
            <div class="ml-auto">
                <svg class="h-4 w-4 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </template>

        {{-- Clear button (hidden when required=true) --}}
        <template x-if="!required && !initializing && !opening && (multiple ? selectedItems.length > 0 : selectedLabel)">
            <button type="button" @click.stop="clear()" class="ml-auto text-gray-400 hover:text-gray-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </template>
    </div>

    {{-- ================================================================ --}}
    {{-- RESULTS + NO-RESULTS DROPDOWNS (single teleport)                 --}}
    {{-- ================================================================ --}}
    <template x-teleport="body">
    <div style="display:contents" @mousedown.stop>

    <div
            x-show="open && results.length > 0"
            style="display: none;"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            data-brcas-dropdown
            data-dropdown
            x-ref="dropdown"
            @scroll="onScroll($event)"
            class="fixed z-50 overflow-auto rounded-md border bg-white shadow-lg {{ $dropdownBorder }}"
            :style="dropdownStyle"
    >
        {{-- Item list --}}
        <template x-for="(item, index) in results" :key="'i' + index">
            <div
                @mousedown.prevent="if (!item._group) selectItem(item)"
                @mouseenter="if (!item._group) highlightedIndex = index"
                :class="item._group
                    ? 'px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-400 select-none cursor-default'
                    : [
                        'cursor-pointer text-gray-700 px-4 py-2 {{ $theme->get('item_text_size') }}',
                        itemHoverBg, itemHoverText,
                        isSelected(item) || highlightedIndex === index ? itemSelectedBg + ' ' + itemSelectedText : '',
                    ]"
            >
                {{-- Group header --}}
                <span x-show="item._group" x-text="item.label"></span>

                {{-- Normal item (without slot) --}}
                <span x-show="!item._group && !hasCustomSlot" class="flex items-center justify-between">
                    <span
                        x-text="isOptionsMode ? item.label : item[labelKey]"
                        :class="item._placeholder ? placeholderText : ''"
                    ></span>
                    <svg x-show="multiple && isSelected(item)" class="h-4 w-4 shrink-0" :class="itemSelectedIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>

                {{-- Normal item (with slot) --}}
                <span x-show="!item._group && hasCustomSlot" class="flex w-full items-center justify-between">
                    <span x-html="renderSlot(item)" class="flex-1"></span>
                    <svg x-show="multiple && isSelected(item)" class="h-4 w-4 shrink-0" :class="itemSelectedIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
            </div>
        </template>

        {{-- Loading more spinner --}}
        <div x-show="loadingMore" class="flex items-center justify-center py-2">
            <svg class="h-4 w-4 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </div>

        {{-- Footer with total count (URL mode only) --}}
        <div x-show="!isOptionsMode && total !== null" class="sticky bottom-0 border-t px-3 py-1.5 text-left text-xs select-none {{ $footerBorder }} {{ $footerBg }} {{ $footerText }}"
             x-text="i18nFooter.replace(':showing', results.length).replace(':total', total)"
        ></div>
    </div>

    {{-- ================================================================ --}}
    {{-- NO RESULTS                                                        --}}
    {{-- ================================================================ --}}
    <div
            x-show="open && !loading && results.length === 0 && hasSearched"
            style="display: none;"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            data-brcas-dropdown
            class="fixed z-50 rounded-md border px-4 py-3 text-sm shadow-lg {{ $emptyBorder }} {{ $emptyBg }} {{ $emptyText }}"
            :style="dropdownStyle"
    >
        @lang('No results found.')
    </div>

    </div>{{-- display:contents wrapper --}}
    </template>
    @if ($wireProp)
        <span
            x-cloak
            x-show="typeof $wire?.$errors?.has === 'function' ? $wire.$errors.has('{{ $wireProp }}') : @js($wireError !== null)"
            x-text="typeof $wire?.$errors?.first === 'function' ? ($wire.$errors.first('{{ $wireProp }}') ?? '') : @js($wireError ?? '')"
            class="text-sm text-red-600 dark:text-red-400"
        ></span>
    @endif
    </div>
</div>
