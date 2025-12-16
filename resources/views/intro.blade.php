@extends('layouts.main')

@push('styles')
<style>
.intro-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.intro-header {
    text-align: center;
    margin-bottom: 50px;
}

.intro-header h1 {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 20px;
}

.intro-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    margin-bottom: 50px;
}

.intro-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #666;
}

.intro-image {
    text-align: center;
}

.intro-image img {
    max-width: 100%;
    border-radius: 10px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.feature-item {
    text-align: center;
    padding: 30px 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.feature-item i {
    font-size: 3rem;
    color: #e91e63;
    margin-bottom: 20px;
}

.feature-item h3 {
    margin-bottom: 15px;
    color: #333;
}

@media (max-width: 768px) {
    .intro-content {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')

<div class="intro-container">
    <div class="intro-header">
        <h1>Về Len Lab</h1>
        <p>Cửa hàng đan móc thủ công chất lượng cao</p>
    </div>

    <div class="intro-content">
        <div class="intro-text">
            <h2>Câu chuyện của chúng tôi</h2>
            <p>
                Len Lab được thành lập với niềm đam mê tạo ra những sản phẩm len thủ công chất lượng cao. 
                Chúng tôi tin rằng mỗi sản phẩm đều mang trong mình tình yêu và sự tỉ mỉ của người thợ thủ công.
            </p>
            <p>
                Với đội ngũ thợ thủ công giàu kinh nghiệm và nguyên liệu len cao cấp được nhập khẩu từ các nước 
                có truyền thống dệt len lâu đời, chúng tôi cam kết mang đến cho khách hàng những sản phẩm 
                không chỉ đẹp mắt mà còn bền vững theo thời gian.
            </p>
        </div>
        <div class="intro-image">
            <img src="{{ asset('banner.png') }}" alt="Len Lab Workshop">
        </div>
    </div>

    <div class="features-grid">
        <div class="feature-item">
            <i class="fas fa-heart"></i>
            <h3>Thủ công 100%</h3>
            <p>Mỗi sản phẩm đều được làm thủ công với tình yêu và sự tỉ mỉ</p>
        </div>

        <div class="feature-item">
            <i class="fas fa-leaf"></i>
            <h3>Nguyên liệu tự nhiên</h3>
            <p>Sử dụng len tự nhiên cao cấp, an toàn cho sức khỏe</p>
        </div>

        <div class="feature-item">
            <i class="fas fa-award"></i>
            <h3>Chất lượng đảm bảo</h3>
            <p>Cam kết chất lượng sản phẩm và dịch vụ khách hàng tốt nhất</p>
        </div>

        <div class="feature-item">
            <i class="fas fa-shipping-fast"></i>
            <h3>Giao hàng nhanh</h3>
            <p>Giao hàng toàn quốc, đóng gói cẩn thận</p>
        </div>
    </div>
</div>

@endsection