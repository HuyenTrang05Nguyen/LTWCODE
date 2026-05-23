// Nạp thư viện Axios giúp gửi các yêu cầu HTTP (GET, POST, PUT, DELETE) ẩn dưới nền bằng AJAX
import axios from 'axios';

// Gán Axios vào đối tượng toàn cục 'window' để có thể gọi và sử dụng thư viện này ở bất kỳ file JS nào khác trong dự án
window.axios = axios;

// Cấu hình mặc định cho tất cả các Header gửi đi từ Axios:
// Ép thuộc tính 'X-Requested-With' có giá trị là 'XMLHttpRequest' để hệ thống Laravel nhận diện được 
// đây là một yêu cầu AJAX (bất đồng bộ) chứ không phải là một lượt tải lại trang (Reload) thông thường.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';