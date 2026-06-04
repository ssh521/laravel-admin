// 모달 관련 공통 유틸리티 함수들

/**
 * 마우스/터치 이벤트에서 클라이언트 좌표 추출
 */
export function getClientCoordinates(event) {
    if (event.touches && event.touches.length > 0) {
        return {
            x: event.touches[0].clientX,
            y: event.touches[0].clientY
        };
    }
    return {
        x: event.clientX,
        y: event.clientY
    };
}

/**
 * 화면 경계 내에서 위치 제한
 */
export function clampPosition(x, y, width, height) {
    const maxX = window.innerWidth - width;
    const maxY = window.innerHeight - height;
    
    return {
        x: Math.max(-(width - 100), Math.min(x, maxX)),
        y: Math.max(0, Math.min(y, maxY))
    };
}

/**
 * 크기를 최소/최대 값으로 제한
 */
export function clampSize(width, height, minWidth, minHeight, maxWidth = null, maxHeight = null) {
    const maxW = maxWidth ?? window.innerWidth - 50;
    const maxH = maxHeight ?? window.innerHeight - 50;
    
    return {
        width: Math.max(minWidth, Math.min(width, maxW)),
        height: Math.max(minHeight, Math.min(height, maxH))
    };
}

/**
 * 드래그/리사이즈 이벤트 리스너 추가
 */
export function addDragResizeListeners(onMove, onEnd) {
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onEnd);
    window.addEventListener('touchmove', onMove, { passive: false });
    window.addEventListener('touchend', onEnd);
}

/**
 * 드래그/리사이즈 이벤트 리스너 제거
 */
export function removeDragResizeListeners(onMove, onEnd) {
    window.removeEventListener('mousemove', onMove);
    window.removeEventListener('mouseup', onEnd);
    window.removeEventListener('touchmove', onMove);
    window.removeEventListener('touchend', onEnd);
}

/**
 * 드래그 핸들러 생성 (공통 로직)
 */
export function createDragHandler(config) {
    const {
        getPos,
        setPos,
        getSize,
        onDragStart,
        onDragMove,
        onDragEnd
    } = config;

    return function handleDragStart(event) {
        event.preventDefault();
        event.stopPropagation();

        if (onDragStart) {
            onDragStart();
        }

        const coords = getClientCoordinates(event);
        const pos = getPos();
        const size = getSize();
        
        const dragOffset = {
            x: coords.x - pos.x,
            y: coords.y - pos.y
        };

        const handleMove = (e) => {
            const newCoords = getClientCoordinates(e);
            const newX = newCoords.x - dragOffset.x;
            const newY = newCoords.y - dragOffset.y;
            
            const clamped = clampPosition(newX, newY, size.w, size.h);
            
            if (onDragMove) {
                onDragMove(clamped.x, clamped.y);
            } else {
                setPos(clamped.x, clamped.y);
            }
        };

        const handleEnd = () => {
            removeDragResizeListeners(handleMove, handleEnd);
            
            if (onDragEnd) {
                onDragEnd();
            }
        };

        addDragResizeListeners(handleMove, handleEnd);
    };
}

/**
 * 리사이즈 핸들러 생성 (공통 로직)
 */
export function createResizeHandler(config) {
    const {
        getPos,
        setPos,
        getSize,
        setSize,
        minWidth,
        minHeight,
        maxWidth,
        maxHeight,
        direction,
        onResizeStart,
        onResizeMove,
        onResizeEnd
    } = config;

    return function handleResizeStart(event) {
        event.preventDefault();
        event.stopPropagation();

        if (onResizeStart) {
            onResizeStart();
        }

        const coords = getClientCoordinates(event);
        const pos = getPos();
        const size = getSize();
        
        const resizeOrigin = {
            x: coords.x,
            y: coords.y,
            w: size.w,
            h: size.h,
            posX: pos.x,
            posY: pos.y
        };

        const handleMove = (e) => {
            const newCoords = getClientCoordinates(e);
            const deltaX = newCoords.x - resizeOrigin.x;
            const deltaY = newCoords.y - resizeOrigin.y;

            let newWidth = resizeOrigin.w;
            let newHeight = resizeOrigin.h;
            let newPosX = resizeOrigin.posX;
            let newPosY = resizeOrigin.posY;

            // 방향에 따른 크기 조정
            if (direction.includes('e')) newWidth += deltaX;
            if (direction.includes('w')) {
                newWidth -= deltaX;
                newPosX += deltaX;
            }
            if (direction.includes('s')) newHeight += deltaY;
            if (direction.includes('n')) {
                newHeight -= deltaY;
                newPosY += deltaY;
            }

            // 크기 제한
            const clampedSize = clampSize(newWidth, newHeight, minWidth, minHeight, maxWidth, maxHeight);
            
            // 위치 조정 (크기가 제한되었을 때)
            if (direction.includes('w')) {
                newPosX = clampedSize.width !== newWidth ? pos.x : Math.max(0, newPosX);
            }
            if (direction.includes('n')) {
                newPosY = clampedSize.height !== newHeight ? pos.y : Math.max(0, newPosY);
            }

            if (onResizeMove) {
                onResizeMove(clampedSize.width, clampedSize.height, newPosX, newPosY);
            } else {
                setSize(clampedSize.width, clampedSize.height);
                setPos(newPosX, newPosY);
            }
        };

        const handleEnd = () => {
            removeDragResizeListeners(handleMove, handleEnd);
            
            if (onResizeEnd) {
                onResizeEnd();
            }
        };

        addDragResizeListeners(handleMove, handleEnd);
    };
}
