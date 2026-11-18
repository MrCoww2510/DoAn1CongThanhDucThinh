<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class SupportController extends Controller
{
    public function shoppingGuide(): void
    {
        $this->showPage('shopping-guide');
    }

    public function warranty(): void
    {
        $this->showPage('warranty');
    }

    public function returns(): void
    {
        $this->showPage('returns');
    }

    public function shipping(): void
    {
        $this->showPage('shipping');
    }

    public function faq(): void
    {
        $this->showPage('faq');
    }

    public function promotions(): void
    {
        $this->showPage('promotions');
    }

    public function techNews(): void
    {
        $this->showPage('tech-news');
    }

    private function showPage(string $slug): void
    {
        $pages = [
            'shopping-guide' => [
                'title' => 'Hướng dẫn mua hàng',
                'intro' => 'Các bước đặt mua sản phẩm trên T&T Computer.',
                'sections' => [
                    [
                        'title' => '1. Tìm sản phẩm phù hợp',
                        'body' => 'Duyệt danh mục hoặc dùng ô tìm kiếm để xem thông tin, giá bán và tình trạng hàng của sản phẩm.',
                    ],
                    [
                        'title' => '2. Thêm vào giỏ hàng',
                        'body' => 'Tại thẻ sản phẩm, chọn Thêm để tiếp tục mua sắm hoặc Mua ngay để chuyển thẳng đến giỏ hàng.',
                    ],
                    [
                        'title' => '3. Kiểm tra giỏ hàng',
                        'body' => 'Xem lại sản phẩm và số lượng. Bạn có thể cập nhật số lượng hoặc xóa sản phẩm trước khi đặt hàng.',
                    ],
                    [
                        'title' => '4. Điền thông tin nhận hàng',
                        'body' => 'Tại trang thanh toán, nhập thông tin người nhận và địa chỉ giao hàng chính xác, sau đó chọn phương thức thanh toán được hiển thị.',
                    ],
                    [
                        'title' => '5. Theo dõi đơn hàng',
                        'body' => 'Sau khi đặt hàng thành công, xem trạng thái và chi tiết đơn trong mục Đơn hàng của tài khoản.',
                    ],
                ],
            ],
            'warranty' => [
                'title' => 'Chính sách bảo hành',
                'intro' => 'Thông tin bảo hành có thể khác nhau tùy sản phẩm và nhà sản xuất.',
                'sections' => [
                    [
                        'title' => 'Thời hạn và điều kiện',
                        'body' => 'Vui lòng kiểm tra thời hạn, phạm vi và điều kiện bảo hành trên thông tin sản phẩm, phiếu bảo hành hoặc chứng từ đi kèm khi nhận hàng.',
                    ],
                    [
                        'title' => 'Chuẩn bị khi yêu cầu hỗ trợ',
                        'body' => 'Giữ lại mã đơn hàng, hóa đơn và phụ kiện đi kèm. Mô tả rõ tình trạng lỗi để bộ phận hỗ trợ hướng dẫn bước tiếp theo.',
                    ],
                    [
                        'title' => 'Gửi yêu cầu bảo hành',
                        'body' => 'Liên hệ T&T Computer qua thông tin hỗ trợ ở cuối trang và cung cấp mã đơn hàng cùng thông tin sản phẩm cần kiểm tra.',
                    ],
                ],
            ],
            'returns' => [
                'title' => 'Chính sách đổi trả',
                'intro' => 'Cách xử lý đổi hoặc trả hàng phụ thuộc tình trạng sản phẩm và điều kiện áp dụng cho đơn hàng.',
                'sections' => [
                    [
                        'title' => 'Kiểm tra sản phẩm khi nhận',
                        'body' => 'Vui lòng kiểm tra ngoại quan, số lượng và phụ kiện ngay khi nhận hàng. Nếu có vấn đề, hãy ghi nhận tình trạng và liên hệ hỗ trợ sớm.',
                    ],
                    [
                        'title' => 'Điều kiện tiếp nhận',
                        'body' => 'Sản phẩm cần được đối chiếu với thông tin đơn hàng và tình trạng thực tế. Một số mặt hàng có thể có điều kiện đổi trả riêng được nêu tại trang sản phẩm hoặc chứng từ đi kèm.',
                    ],
                    [
                        'title' => 'Gửi yêu cầu',
                        'body' => 'Cung cấp mã đơn hàng, sản phẩm cần hỗ trợ và hình ảnh hoặc mô tả tình trạng để T&T Computer kiểm tra và hướng dẫn.',
                    ],
                ],
            ],
            'shipping' => [
                'title' => 'Chính sách vận chuyển',
                'intro' => 'Phí và thời gian giao hàng được xác định theo địa chỉ nhận và thông tin của đơn hàng.',
                'sections' => [
                    [
                        'title' => 'Địa chỉ nhận hàng',
                        'body' => 'Nhập đầy đủ tỉnh/thành, quận/huyện, phường/xã, tên người nhận và số điện thoại để việc giao hàng thuận tiện.',
                    ],
                    [
                        'title' => 'Phí và thời gian giao',
                        'body' => 'Thông tin phí vận chuyển và thời gian dự kiến sẽ được hiển thị hoặc xác nhận trong quá trình đặt hàng, tùy khu vực và phương thức giao hàng.',
                    ],
                    [
                        'title' => 'Theo dõi và nhận hàng',
                        'body' => 'Theo dõi trạng thái trong mục Đơn hàng. Khi nhận, vui lòng kiểm tra tình trạng kiện hàng và sản phẩm bên trong.',
                    ],
                ],
            ],
            'faq' => [
                'title' => 'Câu hỏi thường gặp (FAQ)',
                'intro' => 'Một số câu trả lời nhanh về việc mua sắm và quản lý đơn hàng.',
                'sections' => [
                    [
                        'title' => 'Làm sao để biết sản phẩm còn hàng?',
                        'body' => 'Tình trạng còn hàng được hiển thị ngay trên thẻ sản phẩm và trang chi tiết sản phẩm.',
                    ],
                    [
                        'title' => 'Tôi có thể sửa đơn sau khi đặt không?',
                        'body' => 'Hãy liên hệ hỗ trợ và cung cấp mã đơn hàng sớm nhất. Khả năng cập nhật phụ thuộc trạng thái xử lý đơn.',
                    ],
                    [
                        'title' => 'Xem trạng thái đơn hàng ở đâu?',
                        'body' => 'Đăng nhập tài khoản và mở mục Đơn hàng để xem danh sách và chi tiết các đơn đã đặt.',
                    ],
                    [
                        'title' => 'Tôi cần hỗ trợ bảo hành hoặc đổi trả thì làm gì?',
                        'body' => 'Mở trang Bảo hành hoặc Đổi trả để xem hướng dẫn, sau đó liên hệ bộ phận hỗ trợ kèm mã đơn hàng.',
                    ],
                    [
                        'title' => 'Liên hệ T&T Computer bằng cách nào?',
                        'body' => 'Gửi email đến support@ttcomputer.com hoặc sử dụng thông tin liên hệ ở cuối trang.',
                    ],
                ],
            ],
            'promotions' => [
                'title' => 'Khuyến mãi',
                'intro' => 'Cập nhật các ưu đãi và chương trình mua sắm đang được giới thiệu tại T&T Computer.',
                'sections' => [
                    [
                        'title' => 'Ưu đãi linh kiện máy tính',
                        'body' => 'Khám phá các sản phẩm máy tính, linh kiện và thiết bị văn phòng với mức giá cạnh tranh. Ưu đãi cụ thể được cập nhật tại trang sản phẩm.',
                    ],
                    [
                        'title' => 'Mã giảm giá và quà tặng',
                        'body' => 'Một số chương trình có thể áp dụng mã voucher hoặc quà tặng kèm. Vui lòng kiểm tra điều kiện sử dụng và thời hạn của từng ưu đãi trước khi đặt hàng.',
                    ],
                    [
                        'title' => 'Cách xem ưu đãi',
                        'body' => 'Truy cập danh mục sản phẩm để xem giá bán và thông tin khuyến mãi hiện có. Chương trình có thể thay đổi tùy sản phẩm và thời điểm.',
                    ],
                ],
            ],
            'tech-news' => [
                'title' => 'Tin tức công nghệ',
                'intro' => 'Góc thông tin về máy tính, linh kiện và những xu hướng công nghệ dành cho bạn đọc T&T Computer.',
                'sections' => [
                    [
                        'title' => 'Chọn cấu hình máy tính phù hợp',
                        'body' => 'Khi chọn máy tính, hãy xác định nhu cầu chính như học tập, làm việc văn phòng, thiết kế hay chơi game. Từ đó cân đối bộ xử lý, bộ nhớ, card đồ họa và ngân sách.',
                    ],
                    [
                        'title' => 'Mẹo nâng cấp máy tính',
                        'body' => 'Nâng cấp SSD có thể giúp máy khởi động và mở ứng dụng nhanh hơn; bổ sung RAM phù hợp giúp xử lý nhiều tác vụ cùng lúc. Hãy kiểm tra khả năng tương thích trước khi chọn linh kiện.',
                    ],
                    [
                        'title' => 'Cập nhật nội dung',
                        'body' => 'Các bài viết mới sẽ được bổ sung tại đây. Bạn có thể tham khảo danh mục sản phẩm để tìm hiểu thêm về các thiết bị đang kinh doanh.',
                    ],
                ],
            ],
        ];

        $page = $pages[$slug] ?? null;
        if ($page === null) {
            http_response_code(404);
            $page = [
                'title' => 'Không tìm thấy nội dung hỗ trợ',
                'intro' => 'Nội dung bạn đang tìm không tồn tại.',
                'sections' => [],
            ];
        }

        $this->view('pages/support', [
            'pageTitle' => $page['title'],
            'supportPage' => $page,
        ]);
    }
}
