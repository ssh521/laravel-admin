// 사이드바 배경 관리 컴포넌트 (다크모드 시에도 배경 변경 없음)
document.addEventListener('alpine:init', () => {
    Alpine.data('sidebarBackground', () => ({
        get backgroundStyle() {
            return 'background-image: url(\'/images/menu_bg.gif\'); background-repeat: repeat; background-size: auto;';
        }
    }));
}); 