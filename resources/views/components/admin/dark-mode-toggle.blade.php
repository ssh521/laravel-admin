<!-- resources/views/components/dark-mode-toggle.blade.php -->
<div {{ $attributes->merge(['class' => '-mr-3']) }}>
  <button x-data="{
              dark: false,
              getStoredTheme() {
                  try {
                      return window.localStorage?.getItem('theme') || null;
                  } catch (error) {
                      return null;
                  }
              },
              setStoredTheme(theme) {
                  try {
                      window.localStorage?.setItem('theme', theme);
                  } catch (error) {
                  }
              },
              updateDarkState() {
                  const theme = this.getStoredTheme();
                  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                  const isDark = theme === 'dark' || (theme === 'system' && prefersDark) || (!theme && prefersDark);
                  this.dark = isDark;
              },
              toggleDarkState() {
                  this.dark = !this.dark;
                  this.setStoredTheme(this.dark ? 'dark' : 'light');
                  document.documentElement.classList.toggle('dark', this.dark);
                  window.dispatchEvent(new Event('storage'));
              },
              init() {
                  this.updateDarkState();
                  // localStorage 변경 감지
                  window.addEventListener('storage', () => this.updateDarkState());
                  // 같은 탭에서의 localStorage 변경 감지 (storage 이벤트는 다른 탭에서만 발생)
                  if (window.localStorage) {
                      const originalSetItem = window.localStorage.setItem;
                      window.localStorage.setItem = function(...args) {
                          originalSetItem.apply(this, args);
                          window.dispatchEvent(new Event('storage'));
                      };
                  }
                  // DOM 변경 감지 (appearance 페이지에서 직접 클래스를 변경하는 경우)
                  const observer = new MutationObserver(() => this.updateDarkState());
                  observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                  // Livewire 네비게이션 후에도 상태 업데이트
                  document.addEventListener('livewire:navigated', () => this.updateDarkState());
              }
          }"
          @click="toggleDarkState()"
          class="w-10 h-10 p-0 flex items-center justify-center rounded-full transition-colors bg-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200">
      <!-- 태양 아이콘 (라이트 모드로 전환 액션 표시) -->
      <svg x-show="!dark" x-cloak class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="12" r="4"/>
        <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
      </svg>

      <!-- 달 아이콘 (다크 모드로 전환 액션 표시) -->
      <svg x-show="dark" x-cloak class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 1 0 9.79 9.79z"/>
      </svg>
      <span class="sr-only" x-text="dark ? '라이트 모드' : '다크 모드'"></span>
  </button>
</div>
