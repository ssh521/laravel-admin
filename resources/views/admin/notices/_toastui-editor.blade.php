@once
    <!-- toastui-editor -->
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
    <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js" defer></script>

    <style>
        /* Toast UI Editor 미리보기 이미지 스타일 */
        .toastui-editor-md-preview img {
            max-width: 100% !important;
            height: auto !important;
            display: block !important;
            margin: 10px 0 !important;
        }

        /* 에디터 영역 스타일 */
        .toastui-editor-defaultUI {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
    
        .dark .toastui-editor-defaultUI {
            background: #ffffff;
            border: 1px solid #4b5563;
        }

        .toastui-editor-full-screen {
            position: fixed !important;
            top: 26px;
            left: 0;
            width: 100% !important;
            height: 100vh !important;
            z-index: 9999;
            background: white;
        }

        .dark .toastui-editor-full-screen {
            background: #1f2937;
        }

        .toastui-editor-toolbar-icons.fullscreen {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 22px !important;
            height: 22px !important;
            margin: 0 !important;
            padding: 0 !important;
            background: none !important;
            border: none !important;
            cursor: pointer;
        }
    </style>

    <script>
        window.initToastUiEditorForTextarea = function ({
            editorElementId,
            textareaId,
            formSelector = 'form',
            height = '500px',
            initialEditType = 'wysiwyg',
            imageUploadUrl = null,
        }) {
            const editorElement = document.getElementById(editorElementId);
            const textarea = document.getElementById(textareaId);
            const formElement = document.querySelector(formSelector);

            if (!editorElement || !textarea || !formElement) {
                return;
            }

            if (!window.toastui || !window.toastui.Editor) {
                return;
            }

            const createFullscreenButton = (toggleCallback) => {
                const button = document.createElement('button');
                button.className = 'toastui-editor-toolbar-icons fullscreen';
                button.title = '전체화면';
                button.type = 'button';
                button.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3"></path>
                        <path d="M21 8V5a2 2 0 0 0-2-2h-3"></path>
                        <path d="M3 16v3a2 2 0 0 0 2 2h3"></path>
                        <path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>
                    </svg>
                `;
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    toggleCallback();
                });
                return button;
            };

            const handleFullscreen = (editor) => {
                const editorEl = editorElement.parentElement;
                if (!editorEl) {
                    return;
                }

                const isFullscreen = editorEl.classList.contains('toastui-editor-full-screen');
                const newHeight = isFullscreen ? height : '100vh';

                editor.setHeight(newHeight);
                editorEl.classList.toggle('toastui-editor-full-screen');
                document.body.style.overflow = isFullscreen ? '' : 'hidden';
            };

            const editor = new toastui.Editor({
                el: editorElement,
                height,
                initialEditType,
                previewStyle: 'vertical',
                initialValue: textarea.value || '',
                usageStatistics: false,
                toolbarItems: [
                    ['heading', 'bold', 'italic', 'strike'],
                    ['hr', 'quote'],
                    ['ul', 'ol', 'task', 'indent', 'outdent'],
                    ['table', 'image', 'link'],
                    ['code', 'codeblock'],
                    [
                        {
                            el: createFullscreenButton(() => handleFullscreen(editor)),
                            name: 'fullscreen',
                            tooltip: '전체화면',
                        },
                    ],
                ],
                hooks: imageUploadUrl
                    ? {
                          addImageBlobHook(blob, callback) {
                              const token =
                                  document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

                              const formData = new FormData();
                              formData.append('image', blob);
                              formData.append('_token', token);

                              fetch(imageUploadUrl, {
                                  method: 'POST',
                                  body: formData,
                                  headers: {
                                      'X-Requested-With': 'XMLHttpRequest',
                                  },
                              })
                                  .then((response) => {
                                      if (!response.ok) {
                                          throw new Error(`HTTP error! status: ${response.status}`);
                                      }
                                      return response.json();
                                  })
                                  .then((data) => {
                                      if (data.success && data.url) {
                                          callback(data.url, data.filename || 'uploaded-image');
                                          return;
                                      }

                                      alert('이미지 업로드 실패: ' + (data.message || '알 수 없는 오류'));
                                  })
                                  .catch((error) => {
                                      alert('이미지 업로드 중 오류가 발생했습니다: ' + error.message);
                                  });
                          },
                      }
                    : {},
            });

            // JS가 정상 로드된 경우에만 textarea를 숨겨 progressive enhancement 유지
            textarea.style.display = 'none';

            editor.on('change', () => {
                textarea.value = editor.getMarkdown();
            });

            formElement.addEventListener('submit', function () {
                textarea.value = editor.getMarkdown();
            });
        };
    </script>
@endonce


