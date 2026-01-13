<?php 
//include 'data_zoo.php'; 
require_once 'data_zoo.php';

$id = $_GET['id'] ?? 'xb1c1s1';
$content = $courses[$id] ?? $courses['xb1c1s1']; // 默认 fallback
$slides = $content['ppt'];

// --- 关键修改点：自动解析书籍ID (bid) ---
// 逻辑：截取课程ID的前3位（如 'xb1', 'xb2', 'xb3'）作为书籍ID
// 这样无论以后加 'xb4' 还是 'xb5'，都能自动正确跳转
$bid = substr($id, 0, 3); 

// 如果截取失败或不在书籍列表中，默认回 xb1
if (!isset($books[$bid])) {
    $bid = 'xb1';
}

// 获取当前课程数据
$page = $courses[$id] ?? null;

// 如果课程不存在
if (!$page) {
    echo "<h1>未找到课程: $id</h1><a href='index.php'>返回首页</a>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $content['title']; ?></title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcdn.net/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        /* === 基础布局 === */
        body { background: #e0f7fa; font-family: 'Segoe UI', sans-serif; height: 100vh; height: 100dvh; overflow: hidden; display: flex; flex-direction: column; }
        .stage { flex: 1; display: flex; align-items: center; justify-content: center; background: linear-gradient(to bottom, #b3e5fc, #fff); overflow: hidden; width: 100%; padding: 10px; }
        .slide-card { width: 100%; max-width: 960px; height: 100%; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: none; flex-direction: column; overflow: hidden; border: 1px solid #b3e5fc; position: relative; }
        .slide-card.active { display: flex; }
        .role-header { padding: 20px 30px; color: white; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .role-header h2 { font-size: 1.5rem; margin: 0; font-weight: bold; }
        .role-avatar { font-size: 3rem; filter: drop-shadow(2px 2px 0 rgba(0,0,0,0.2)); }
        .content-body { padding: 30px 50px; flex: 1; overflow-y: auto; font-size: 1.4rem; color: #333; line-height: 1.6; }
        .nav-bar { background: white; padding: 15px; text-align: center; border-top: 1px solid #eee; flex-shrink: 0; padding-bottom: max(15px, env(safe-area-inset-bottom)); z-index: 100; }
        .visual-box { margin: 20px auto; text-align: center; height: 350px; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:15px; background:#f8f9fa; position:relative; width: 100%; }
        .icon-large { font-size: 8rem; animation: float 3s ease-in-out infinite; }
        
        /* 移动端适配 */
        @media (max-width: 768px) {
            .role-header { padding: 15px 20px; }
            .role-header h2 { font-size: 1.2rem; }
            .content-body { padding: 15px 20px; font-size: 1.1rem; }
            .visual-box { height: 250px; margin: 10px auto; }
            .icon-large { font-size: 5rem; }
            /* 缩小所有特效容器 */
            .css_population_map, .css_push_pull, .css_barrel, .css-solar, .css-thermal, .css-water-cycle, .css-typhoon, .css-flood, .css-gnss-pin { transform: scale(0.7); transform-origin: center; }
        }

        /* === 动画关键帧 === */
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        @keyframes blink { 0%,100%{opacity: 1;} 50%{opacity: 0.2;} }
        @keyframes moveRight { 0%{transform: translateX(0); opacity: 0;} 50%{opacity: 1;} 100%{transform: translateX(20px); opacity: 0;} }
        @keyframes flashRed { 0%,100%{background: #c0392b;} 50%{background: #e74c3c;} }
        @keyframes spin { 100% { transform:translate(-50%,-50%) rotate(360deg); } }
        @keyframes up { 0%{bottom:0;opacity:0} 50%{bottom:120px;opacity:1} 100%{bottom:120px;opacity:0} }
        @keyframes down { 0%{top:20px;opacity:0} 50%{top:140px;opacity:1} 100%{top:140px;opacity:0} }
        @keyframes right { 0%{transform:translateX(-20px);opacity:0} 50%{transform:translateX(0);opacity:1} 100%{transform:translateX(20px);opacity:0} }
        @keyframes rise { 0%{bottom:50px;opacity:1} 100%{bottom:200px;opacity:0} }
        @keyframes fall { 0%{top:50px;opacity:0} 50%{top:150px;opacity:1} }
        @keyframes tide { 0%,100%{height: 80px} 50%{height: 60px} }
        @keyframes windMove { 0%{transform: translateX(0); opacity: 0;} 50%{opacity: 1;} 100%{transform: translateX(50px); opacity: 0;} }
        @keyframes quake { 0%,100%{height: 10px} 50%{height: 100px} }
        @keyframes scan { 0%{top:0} 100%{top:100%} }
        @keyframes floatLayer { from{transform: translateZ(40px)} to{transform: translateZ(60px)} }
        @keyframes bounce { 0%, 100% {transform: translate(-50%, -100%);} 50% {transform: translate(-50%, -120%);} }
        @keyframes orbit { from { transform: rotate(0deg) translateX(100px) rotate(0deg); } to { transform: rotate(360deg) translateX(100px) rotate(-360deg); } }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        @keyframes shake { 0%{transform:translateX(0)} 25%{transform:translateX(5px)} 75%{transform:translateX(-5px)} 100%{transform:translateX(0)} }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        @keyframes spin { 100% { transform:rotate(360deg); } }
        @keyframes sun-move { 0%{top:20%} 50%{top:80%} 100%{top:20%} }
        @keyframes orbit-move { from { transform: rotate(0deg) translateX(100px) rotate(0deg); } to { transform: rotate(360deg) translateX(100px) rotate(-360deg); } }


        /* === 必修二特效 (哪吒主题) === */
        
        /* 人口分布图 */
        .css_population_map { width: 300px; height: 180px; background: #81d4fa; position: relative; border-radius: 10px; overflow: hidden; border: 2px solid #333; }
        .land-mass { position: absolute; background: #a5d6a7; opacity: 0.8; }
        .land-asia { width: 100px; height: 80px; top: 20px; right: 20px; border-radius: 30% 70% 70% 30%; background: #2ecc71; }
        .pop-dot { width: 6px; height: 6px; background: #c0392b; border-radius: 50%; position: absolute; animation: blink 2s infinite; }
        .dot-1 { top: 40px; right: 80px; } .dot-2 { top: 45px; right: 75px; } .dot-3 { top: 50px; right: 85px; } 

        /* 推拉理论 */
        .css_push_pull { width: 300px; height: 150px; display: flex; align-items: center; justify-content: center; gap: 30px; }
        .zone { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 1.2rem; position: relative; }
        .zone-push { background: #95a5a6; border: 3px dashed #34495e; }
        .zone-push::after { content: '推'; }
        .zone-pull { background: #e74c3c; box-shadow: 0 0 20px rgba(231, 76, 60, 0.5); }
        .zone-pull::after { content: '拉'; }
        .arrow-move { font-size: 40px; color: #2c3e50; animation: moveRight 1.5s infinite; }

        /* 木桶效应 */
        .css_barrel { width: 200px; height: 180px; position: relative; margin-top: 20px; }
        .stave { position: absolute; bottom: 0; width: 20px; background: #d35400; border: 1px solid #5d4037; border-radius: 5px 5px 0 0; }
        .stave-long { height: 150px; background: #e67e22; }
        .stave-short { height: 100px; background: #c0392b; animation: flashRed 2s infinite; } /* 短板 */
        .barrel-water { position: absolute; bottom: 0; left: 10px; width: 180px; height: 100px; background: rgba(52, 152, 219, 0.6); z-index: 10; border-radius: 0 0 10px 10px; border-top: 2px dashed rgba(255,255,255,0.5); }
        /* 排列木板 */
        .s1 { left: 20px; height: 160px; } .s2 { left: 45px; height: 150px; } .s3 { left: 70px; height: 170px; }
        .s4 { left: 95px; height: 100px; } /* 短板 */
        .s5 { left: 120px; height: 150px; } .s6 { left: 145px; height: 160px; }

        /* 图标类 */
        .icon-hu-line { font-size: 6rem; background: linear-gradient(135deg, transparent 48%, red 49%, red 51%, transparent 52%); -webkit-background-clip: text; color: #333; font-weight: bold; }

        /* === 必修一特效 (保留) === */
        /* 太阳系 */
        .css-solar { width:300px; height:300px; position:relative; max-width: 100%; }
        .sun { width:50px; height:50px; background:gold; border-radius:50%; position:absolute; top:125px; left:125px; box-shadow:0 0 30px gold; animation: pulse 2s infinite; }
        .orbit { border:1px solid #ccc; border-radius:50%; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); animation: spin 6s linear infinite; }
        .planet { width:15px; height:15px; background:blue; border-radius:50%; position:absolute; top:-7px; left:50%; margin-left:-7px; }
        
        /* 热力环流 */
        .css-thermal { width:300px; height:200px; border-bottom:5px solid #555; position:relative; max-width: 100%; }
        .arrow { font-size:30px; position:absolute; font-weight:bold; }
        .a-up { color:red; left:40px; animation: up 2s infinite; }
        .a-down { color:blue; right:40px; animation: down 2s infinite; }
        .a-flow { color:#555; top:20px; left:130px; animation: right 2s infinite; }

        /* 水循环 */
        .css-water-cycle { width:400px; height:300px; position:relative; overflow:hidden; max-width: 100%; }
        .ocean { width:100%; height:50px; background:#2980b9; position:absolute; bottom:0; }
        .land { width:150px; height:100px; background:#27ae60; position:absolute; bottom:0; right:0; border-radius:50px 0 0 0; }
        .vapour { font-size:40px; position:absolute; left:50px; animation: rise 3s infinite; }
        .rain { font-size:40px; position:absolute; right:50px; top:50px; animation: fall 1s infinite; color:#3498db; }

        /* === XB1 特效 (爱宠自然地理) === */
        /* 地球自转 */
        .css_rotation { width: 200px; height: 200px; background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Earth_Western_Hemisphere_transparent_background.png/240px-Earth_Western_Hemisphere_transparent_background.png'); background-size: cover; animation: spin 10s linear infinite; border-radius: 50%; box-shadow: inset -20px -20px 50px rgba(0,0,0,0.5); }
        
        /* 昼夜交替 */
        .css_day_night { width: 200px; height: 200px; background: #3498db; border-radius: 50%; position: relative; overflow: hidden; }
        .css_day_night::after { content:''; position: absolute; width: 50%; height: 100%; background: rgba(0,0,0,0.6); right: 0; }
        
        /* 公转轨道 */
        .css_orbit { width: 300px; height: 200px; border: 2px dashed #ccc; border-radius: 50%; position: relative; }
        .css_sun { width: 40px; height: 40px; background: gold; border-radius: 50%; position: absolute; top: 80px; left: 130px; box-shadow: 0 0 20px gold; }
        .css_earth_moving { width: 20px; height: 20px; background: #3498db; border-radius: 50%; position: absolute; top: 90px; left: 140px; animation: orbit-move 5s linear infinite; }

        /* 太阳直射点移动 */
        .css_sun_move { width: 300px; height: 200px; border-left: 2px solid #333; position: relative; }
        .line-n { border-bottom: 1px dashed red; position: absolute; top: 20%; width: 100%; text-align: right; }
        .line-s { border-bottom: 1px dashed blue; position: absolute; top: 80%; width: 100%; text-align: right; }
        .sun-point { width: 30px; height: 30px; background: orange; border-radius: 50%; position: absolute; left: 50%; animation: sun-move 4s infinite ease-in-out; }
        
        /* 褶皱动画 */
        .css_fold { width: 200px; height: 100px; border-top: 5px solid #333; border-radius: 50% 50% 0 0; animation: pulse 2s infinite; }
        /* 断层动画 */
        .css_fault { width: 200px; height: 150px; position: relative; }
        .css_fault::before { content:''; position: absolute; width: 100px; height: 100px; background: #95a5a6; left: 0; bottom: 0; animation: slideDown 2s infinite; }
        .css_fault::after { content:''; position: absolute; width: 100px; height: 100px; background: #7f8c8d; right: 0; top: 0; }
        @keyframes slideDown { 0%{bottom:20px} 50%{bottom:0} 100%{bottom:20px} }

         
        /* 其他 CSS 组件 */
        .css-nuclear { font-size:3rem; font-weight:bold; color:#e67e22; animation: pulse 1s infinite; }
        .css-seismic { width:300px; height:100px; background: repeating-linear-gradient(90deg, #333, #333 2px, transparent 2px, transparent 20px); animation: shake 0.5s infinite; max-width: 100%; }
        .css-karst { width: 300px; height: 150px; position: relative; border-bottom: 5px solid #7f8c8d; max-width: 100%; }
        .peak { position: absolute; bottom: 0; width: 0; height: 0; border-left: 30px solid transparent; border-right: 30px solid transparent; border-bottom: 80px solid #95a5a6; }
        .peak:nth-child(1) { left: 20px; border-bottom-width: 100px; border-bottom-color: #7f8c8d; z-index: 2; }
        .peak:nth-child(2) { left: 60px; border-bottom-width: 140px; z-index: 1; }
        .peak:nth-child(3) { left: 120px; border-bottom-width: 90px; border-bottom-color: #7f8c8d; z-index: 3; }
        .peak:nth-child(4) { left: 180px; border-bottom-width: 120px; z-index: 1; }
        .css-river-valley { width: 300px; height: 200px; background: #a5d6a7; position: relative; overflow: hidden; max-width: 100%; }
        .river-path { width: 400px; height: 100px; border: 20px solid #3498db; border-radius: 50%; position: absolute; top: 50px; left: -50px; transform: rotate(10deg); }
        .river-path::after { content:''; position: absolute; top: 20px; left: 100px; width: 20px; height: 20px; background: #fff; border-radius: 50%; } 
        .css-coast { width: 300px; height: 150px; background: #f1c40f; position: relative; overflow: hidden; max-width: 100%; }
        .coast-water { width: 100%; height: 80px; background: #2980b9; position: absolute; bottom: 0; animation: tide 3s infinite ease-in-out;}
        .css-dune { width: 300px; height: 150px; position: relative; max-width: 100%; }
        .dune-shape { width: 200px; height: 100px; background: #e67e22; border-radius: 100px 100px 0 0; position: absolute; bottom: 0; left: 50px; box-shadow: inset -20px 0 0 rgba(0,0,0,0.1); }
        .wind-arrow { font-size: 30px; position: absolute; top: 20px; left: 20px; animation: windMove 2s infinite; }
        .css-slope-tri { width: 0; height: 0; border-bottom: 150px solid #34495e; border-right: 250px solid transparent; position: relative; margin-top: 50px; }
        .slope-text { position: absolute; top: 60px; left: 50px; color: white; font-weight: bold; transform: rotate(-30deg); }
        .css-zoom { width: 100px; height: 100px; border: 10px solid #34495e; border-radius: 50%; position: relative; }
        .css-zoom::after { content:''; width: 20px; height: 80px; background: #34495e; position: absolute; bottom: -60px; right: -40px; transform: rotate(-45deg); }
        .zoom-target { width: 50px; height: 50px; background: #e74c3c; border-radius: 50%; position: absolute; top: 15px; left: 15px; animation: pulse 1s infinite; }
        .css-height-diff { display: flex; align-items: flex-end; justify-content: center; height: 200px; gap: 20px; }
        .h-bar-1 { width: 50px; height: 100px; background: #95a5a6; }
        .h-bar-2 { width: 50px; height: 180px; background: #2c3e50; position: relative; }
        .h-line { position: absolute; top: 0; left: -70px; width: 120px; border-top: 2px dashed red; }
        .css-veg-layer { width: 300px; height: 200px; position: relative; border-bottom: 5px solid #795548; background: linear-gradient(to bottom, #e1f5fe 0%, #fff 80%); max-width: 100%; }
        .tree-high { font-size: 60px; position: absolute; bottom: 0; left: 20px; color: #2e7d32; }
        .tree-mid { font-size: 40px; position: absolute; bottom: 0; left: 100px; color: #43a047; }
        .tree-low { font-size: 20px; position: absolute; bottom: 0; left: 180px; color: #66bb6a; }
        .grass { font-size: 15px; position: absolute; bottom: 0; right: 20px; color: #81c784; }
        .css-mangrove { width: 300px; height: 180px; position: relative; overflow: hidden; max-width: 100%; }
        .mangrove-water { width: 100%; height: 60px; background: #81d4fa; position: absolute; bottom: 0; opacity: 0.7; }
        .mangrove-roots { width: 100%; height: 80px; position: absolute; bottom: 0; background-image: radial-gradient(circle, transparent 40%, #5d4037 41%); background-size: 30px 40px; background-position: 0 10px; }
        .mangrove-tree { font-size: 80px; position: absolute; bottom: 40px; left: 100px; }
        .css-soil-texture { display: flex; gap: 15px; align-items: flex-end; height: 150px; }
        .soil-pile { width: 60px; border-radius: 50% 50% 5px 5px; position: relative; }
        .pile-sand { height: 60px; background: #fff3e0; border: 2px solid #ffe0b2; }
        .pile-loam { height: 80px; background: #8d6e63; border: 2px solid #5d4037; }
        .pile-clay { height: 60px; background: #bcaaa4; border: 2px solid #795548; }
        .css-soil-profile { width: 120px; height: 200px; border: 2px solid #333; display: flex; flex-direction: column; font-size: 10px; color: white; text-align: center; }
        .profile-o { height: 10%; background: #333; display:flex;align-items:center;justify-content:center; }
        .profile-a { height: 20%; background: #212121; display:flex;align-items:center;justify-content:center; }
        .profile-e { height: 15%; background: #bdbdbd; color:#333; display:flex;align-items:center;justify-content:center; }
        .profile-b { height: 25%; background: #795548; display:flex;align-items:center;justify-content:center; }
        .profile-c { height: 20%; background: #ffe0b2; color:#333; display:flex;align-items:center;justify-content:center; }
        .profile-r { height: 10%; background: #607d8b; display:flex;align-items:center;justify-content:center; }
        .css-forest-types { display: flex; justify-content: space-around; width: 100%; align-items: flex-end; height: 150px; }
        .f-type-rain { font-size: 60px; filter: drop-shadow(2px 2px 0 #1b5e20); }
        .f-type-needle { font-size: 50px; filter: grayscale(0.5); }
        .css-typhoon { width: 300px; height: 300px; position: relative; display: flex; align-items: center; justify-content: center; max-width: 100%; }
        .typhoon-eye { width: 30px; height: 30px; background: white; border-radius: 50%; z-index: 10; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
        .typhoon-arm { position: absolute; width: 100%; height: 100%; border-radius: 50%; border: 15px solid transparent; border-top-color: #ecf0f1; border-right-color: #bdc3c7; animation: spin 2s linear infinite; }
        .typhoon-arm:nth-child(2) { width: 70%; height: 70%; animation-duration: 1.5s; border-top-color: #95a5a6; }
        .typhoon-arm:nth-child(3) { width: 40%; height: 40%; animation-duration: 1s; border-top-color: #7f8c8d; }
        .css-flood { width: 300px; height: 200px; position: relative; overflow: hidden; background: #e0f7fa; border-bottom: 5px solid #0277bd; max-width: 100%; }
        .house-sub { width: 50px; height: 50px; background: #795548; position: absolute; bottom: 0; left: 50px; }
        .house-roof { width: 0; height: 0; border-left: 35px solid transparent; border-right: 35px solid transparent; border-bottom: 40px solid #a1887f; position: absolute; bottom: 50px; left: 40px; }
        .water-level { width: 100%; height: 60%; background: rgba(3, 169, 244, 0.7); position: absolute; bottom: 0; animation: rise 3s infinite alternate; }
        .css-seismic-wave { width: 300px; height: 150px; display: flex; align-items: center; justify-content: center; gap: 5px; max-width: 100%; }
        .wave-bar { width: 10px; background: #e74c3c; animation: quake 1s infinite; }
        .wave-bar:nth-child(odd) { animation-delay: 0.1s; background: #c0392b; }
        .css-rs-scan { width: 250px; height: 200px; background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/World_map_blank_without_borders.svg/300px-World_map_blank_without_borders.svg.png'); background-size: cover; position: relative; overflow: hidden; border: 2px solid #333; }
        .scan-line { width: 100%; height: 2px; background: red; position: absolute; top: 0; box-shadow: 0 0 10px red; animation: scan 2s infinite linear; }
        .css-gis-layer { width: 200px; height: 200px; position: relative; transform: rotateX(60deg) rotateZ(-30deg); transform-style: preserve-3d; }
        .layer-plate { width: 100%; height: 100%; position: absolute; border: 2px solid #333; background: rgba(255,255,255,0.8); transition: 0.5s; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .layer-1 { transform: translateZ(0px); background: rgba(46, 204, 113, 0.5); }
        .layer-2 { transform: translateZ(40px); background: rgba(52, 152, 219, 0.5); animation: floatLayer 2s infinite alternate; }
        .layer-3 { transform: translateZ(80px); background: rgba(231, 76, 60, 0.5); animation: floatLayer 2s infinite alternate-reverse; }
        .css-gnss-pin { width: 300px; height: 200px; position: relative; background: #f0f3f4; max-width: 100%; }
        .pin { font-size: 50px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -100%); color: #e74c3c; animation: bounce 1s infinite; }
        .sat { font-size: 30px; position: absolute; top: 20px; right: 20px; animation: orbit 3s infinite linear; }
        .web-img { height: 100%; width: auto; object-fit: contain; max-width: 100%; }
    </style>
</head>
<body>
    <div class="stage">
        <?php foreach($slides as $k => $s): ?>
        <?php 
            // 默认角色配置
           // $color = '#3498db'; $emoji = '🐰'; $name = '朱迪警官';
           $color = '#3498db'; $emoji = '👮'; $name = '讲师';
            
            // 角色判断逻辑
            if(isset($s['role'])) {
                // B1 角色
                if($s['role']=='nick') { $color='#e67e22'; $emoji='🦊'; $name='尼克'; }
                if($s['role']=='flash') { $color='#27ae60'; $emoji='🦥'; $name='闪电'; }
                if($s['role']=='bogo') { $color='#2c3e50'; $emoji='🐃'; $name='牛局长'; }
                
                // B2 角色 (哪吒主题)
                if($s['role']=='nezha') { $color='#e74c3c'; $emoji='🔥'; $name='哪吒'; }
                if($s['role']=='aobing') { $color='#3498db'; $emoji='🐉'; $name='敖丙'; }
                if($s['role']=='taiyi') { $color='#f1c40f'; $emoji='🍶'; $name='太乙真人'; }
                if($s['role']=='lijing') { $color='#8e44ad'; $emoji='🏯'; $name='李靖'; }
                if($s['role']=='shengongbao') { $color='#2c3e50'; $emoji='🐆'; $name='申公豹'; }

                // 爱宠角色映射
            if($s['role']=='max') { $color='#3498db'; $emoji='🐶'; $name='麦克 (Max)'; } // 忠诚/主角
            if($s['role']=='snowball') { $color='#e74c3c'; $emoji='🐰'; $name='小白 (Snowball)'; } // 疯狂/霸气
            if($s['role']=='duke') { $color='#8d6e63'; $emoji='🐕'; $name='杜老大 (Duke)'; } // 憨厚
            if($s['role']=='gidget') { $color='#fd79a8'; $emoji='🐩'; $name='啾啾 (Gidget)'; } // 机智/浪漫
            if($s['role']=='chloe') { $color='#a29bfe'; $emoji='🐱'; $name='克洛伊 (Chloe)'; } // 高冷
            if($s['role']=='norman') { $color='#f1c40f'; $emoji='🐹'; $name='诺曼 (Norman)'; } // 迷糊
            }
                 
                // XB2 彼得兔角色 (本次新增)
            if($s['role']=='peter') { $color='#3498db'; $emoji='🐰'; $name='彼得兔 (Peter)'; } // 主角/蓝色
            if($s['role']=='benjamin') { $color='#8d6e63'; $emoji='🎩'; $name='本杰明 (Benjamin)'; } // 伙伴/棕色
            if($s['role']=='lily') { $color='#e91e63'; $emoji='🎀'; $name='莉莉 (Lily)'; } // 聪明/粉色
            if($s['role']=='tod') { $color='#e67e22'; $emoji='🦊'; $name='托德先生 (Mr. Tod)'; } // 反派/橙色
            if($s['role']=='jemima') { $color='#f1c40f'; $emoji='🦆'; $name='杰米玛 (Jemima)'; } // 单纯/黄色
            
            // XB3 功夫熊猫角色 (本次新增)
            if($s['role']=='po') { $color='#e74c3c'; $emoji='🐼'; $name='阿宝 (Po)'; } // 主角/红色
            if($s['role']=='shifu') { $color='#8e44ad'; $emoji='🐹'; $name='师父 (Shifu)'; } // 导师/紫色
            if($s['role']=='tigress') { $color='#d35400'; $emoji='🐯'; $name='悍娇虎 (Tigress)'; } // 力量/橙色
            if($s['role']=='ping') { $color='#27ae60'; $emoji='🦢'; $name='平先生 (Mr. Ping)'; } // 民生/绿色
            if($s['role']=='crane') { $color='#3498db'; $emoji='🕊️'; $name='仙鹤 (Crane)'; } // 侦察/蓝色
            
            // 兼容其他书角色 (保留)
            if($s['role']=='max') { $color='#2980b9'; $emoji='🐶'; $name='麦克'; }
            if($s['role']=='snowball') { $color='#c0392b'; $emoji='🐰'; $name='小白'; }
            if($s['role']=='peter') { $color='#3498db'; $emoji='🐰'; $name='彼得兔 (Peter)'; }
        
        ?>
        <div class="slide-card animate__animated animate__<?php echo $s['anim_type'] ?? 'fadeIn'; ?>" id="slide-<?php echo $k; ?>">
            <div class="role-header" style="background: <?php echo $color; ?>">
                <div><h2 class="m-0 fw-bold"><?php echo $s['title']; ?></h2><span style="opacity:0.9"><?php echo $name; ?></span></div>
                <div class="role-avatar"><?php echo $emoji; ?></div>
            </div>
            <div class="content-body">
                <?php echo $s['content']; ?>
                <div class="visual-box">
                    <?php 
                        $v = $s['visual'] ?? 'icon_star';
                        
                        // 使用数组映射处理普通图标，避免冗长的 if/else 结构
                        $icon_map = [
                            'icon_earth' => '🌍', 'icon_rock' => '🪨', 'icon_life' => '🧬',
                            'icon_water_drop' => '💧', 'icon_dam' => '🏗️', 'icon_surf' => '🏄',
                            'icon_mountain' => '🏔️', 'icon_cave' => '🦇', 'icon_compass' => '🧭',
                            'icon_badge' => '👮', 'icon_telescope' => '🔭', 'icon_railway' => '🚂',
                            'icon_map_scatter' => '🗺️', 'icon_backpack' => '🎒', 'icon_tree_planting' => '🌳',
                            'icon_tree' => '🌳', 'icon_leaf_shiny' => '🍃', 'icon_grass' => '🌾',
                            'icon_park' => '🏡', 'icon_forest_map' => '🗺️', 'icon_soil_layers' => '🥪',
                            'icon_soil_comp' => '🧪', 'icon_shovel' => '⛏️', 'icon_rock_plant' => '🪨',
                            'icon_climate_soil' => '🌦️', 'icon_protect_soil' => '🛡️', 'icon_salt' => '🧂',
                            'icon_storm' => '🌪️', 'icon_drought' => '☀️', 'icon_china_map' => '🇨🇳',
                            'icon_balance' => '⚖️', 'icon_cold' => '🥶', 'icon_arrow_map' => '↘️',
                            'icon_sandstorm' => '🏜️', 'icon_alert_red' => '🛑', 'icon_earth_crack' => '🏚️',
                            'icon_compare' => '🆚', 'icon_chain' => '🔗', 'icon_shield' => '🛡️',
                            'icon_alert_yellow' => '⚠️', 'icon_radar' => '📡', 'icon_flood_safe' => '🏊',
                            'icon_run_direction' => '🏃', 'icon_rebuild' => '🏗️', 'icon_kit' => '⛑️',
                            'icon_drill' => '📢', 'icon_money' => '💰', 'icon_siren' => '🚑',
                            'icon_satellite' => '🛰️', 'icon_beidou' => '🌌', 'icon_integration' => '🧩',
                            'icon_phone' => '📱', 'icon_key' => '🔑', 'icon_drone' => '🚁',
                            'icon_final' => '🎓',
                            // 必修二新增
                            'icon_hu_line' => '🇨🇳', 'icon_chart' => '📊', 'icon_baby' => '👶',
                            'icon_train' => '🚄', 'icon_fire' => '🔥', 'icon_factory' => '🏭', 'icon_ship' => '🚢',
                            //XB3新增图标映射
                            'icon_life'=>'🥟', 'icon_chart'=>'📉', 'icon_alert_red'=>'🚨', 
                            'icon_clothing'=>'👕', 'icon_shield'=>'🛡️', 'icon_final'=>'📜', 
                            'icon_fire'=>'🔥', 'icon_coal'=>'⚫', 'icon_oil'=>'🛢️', 
                            'icon_bus'=>'🚌', 'icon_wind'=>'🍃', 'icon_wheat'=>'🌾', 
                            'icon_soil_layers'=>'🥪', 'icon_satellite'=>'🛰️', 'icon_ship'=>'🚢', 
                            'icon_sea'=>'🌊', 'icon_island'=>'🏝️', 'icon_radar'=>'📡', 
                            'icon_integration'=>'🤝'
                        ];

                        // 特殊 CSS 动画渲染
                        if ($v == 'css_population_map') {
                            echo '<div class="css_population_map"><div class="land-mass land-asia"></div><div class="pop-dot dot-1"></div><div class="pop-dot dot-2"></div><div class="pop-dot dot-3"></div></div>';
                        } elseif ($v == 'css_push_pull') {
                            echo '<div class="css_push_pull"><div class="zone zone-push"></div><div class="arrow-move">➡</div><div class="zone zone-pull"></div></div>';
                        } elseif ($v == 'css_barrel' || $v == 'css_barrel_short') {
                            echo '<div class="css_barrel"><div class="barrel-water"></div><div class="stave s1"></div><div class="stave s2"></div><div class="stave s3"></div><div class="stave stave-short s4"></div><div class="stave s5"></div><div class="stave s6"></div></div>';
                        } elseif ($v == 'css_curve') {
                            // 简单的S型曲线模拟
                            echo '<div style="width:200px;height:150px;border-left:2px solid #333;border-bottom:2px solid #333;position:relative"><div style="position:absolute;bottom:0;left:0;width:100%;height:100%;border-top:2px dotted red;transform:skewY(-20deg);"></div><span style="position:absolute;bottom:10px;right:10px">时间</span><span style="position:absolute;top:10px;left:10px">水平</span></div>';
                        } elseif ($v == 'css_solar_system') {
                            echo '<div class="css-solar"><div class="sun"></div><div class="orbit" style="width:200px;height:200px"><div class="planet"></div></div></div>';
                        } elseif ($v == 'css_thermal') {
                            echo '<div class="css-thermal"><div class="arrow a-up">🔥</div><div class="arrow a-down">❄️</div><div class="arrow a-flow">➡️</div></div>';
                        } elseif ($v == 'css_water_cycle') {
                            echo '<div class="css-water-cycle"><div class="ocean"></div><div class="land"></div><div class="vapour">♨️</div><div class="rain">💧</div></div>';
                        } elseif ($v == 'css_nuclear') {
                            echo '<div class="css-nuclear">H+H 💥 He</div>';
                        } elseif ($v == 'css_seismic') {
                            echo '<div class="css-seismic"></div>';
                        } elseif ($v == 'css_karst') {
                            echo '<div class="css-karst"><div class="peak"></div><div class="peak"></div><div class="peak"></div><div class="peak"></div></div>';
                        } elseif ($v == 'css_river_valley') {
                            echo '<div class="css-river-valley"><div class="river-path"></div></div>';
                        } elseif ($v == 'css_dune') {
                            echo '<div class="css-dune"><div class="wind-arrow">💨</div><div class="dune-shape"></div></div>';
                        } elseif ($v == 'css_coast') {
                            echo '<div class="css-coast"><div class="coast-water"></div></div>';
                        } elseif ($v == 'css_slope_tri') {
                            echo '<div class="css-slope-tri"><div class="slope-text">Slope</div></div>';
                        } elseif ($v == 'css_zoom') {
                            echo '<div class="css-zoom"><div class="zoom-target"></div></div>';
                        } elseif ($v == 'css_height_diff') {
                            echo '<div class="css-height-diff"><div class="h-bar-1"></div><div class="h-bar-2"><div class="h-line"></div></div></div>';
                        } elseif ($v == 'css_veg_layer') {
                            echo '<div class="css-veg-layer"><div class="tree-high">🌳</div><div class="tree-mid">🌲</div><div class="tree-low">🌿</div><div class="grass">🌱</div></div>';
                        } elseif ($v == 'css_mangrove') {
                            echo '<div class="css-mangrove"><div class="mangrove-roots"></div><div class="mangrove-water"></div><div class="mangrove-tree">🌳</div></div>';
                        } elseif ($v == 'css_soil_texture') {
                            echo '<div class="css-soil-texture"><div class="soil-pile pile-sand" title="砂土"></div><div class="soil-pile pile-loam" title="壤土"></div><div class="soil-pile pile-clay" title="黏土"></div></div>';
                        } elseif ($v == 'css_soil_profile') {
                            echo '<div class="css-soil-profile"><div class="profile-o">O</div><div class="profile-a">A</div><div class="profile-e">E</div><div class="profile-b">B</div><div class="profile-c">C</div><div class="profile-r">R</div></div>';
                        } elseif ($v == 'css_forest_types') {
                            echo '<div class="css-forest-types"><div class="f-type-rain">🌴<br><span style="font-size:12px;color:#333">雨林</span></div><div class="f-type-needle">🌲<br><span style="font-size:12px;color:#333">针叶</span></div></div>';
                        } elseif ($v == 'css_typhoon') {
                            echo '<div class="css-typhoon"><div class="typhoon-eye"></div><div class="typhoon-arm"></div><div class="typhoon-arm"></div><div class="typhoon-arm"></div></div>';
                        } elseif ($v == 'css_flood') {
                            echo '<div class="css-flood"><div class="house-sub"><div class="house-roof"></div></div><div class="water-level"></div></div>';
                        } elseif ($v == 'css_seismic_wave') {
                            echo '<div class="css-seismic-wave"><div class="wave-bar"></div><div class="wave-bar"></div><div class="wave-bar"></div><div class="wave-bar"></div><div class="wave-bar"></div></div>';
                        } elseif ($v == 'css_rs_scan') {
                            echo '<div class="css-rs-scan"><div class="scan-line"></div></div>';
                        } elseif ($v == 'css_gis_layer') {
                            echo '<div class="css-gis-layer"><div class="layer-plate layer-1">地形</div><div class="layer-plate layer-2">水系</div><div class="layer-plate layer-3">人口</div></div>';
                        } elseif ($v == 'css_gnss_pin') {
                            echo '<div class="css-gnss-pin"><div class="pin">📍</div><div class="sat">🛰️</div></div>';
                        } elseif ($v == 'css_landslide') {
                            echo '<div class="icon-large" style="transform:rotate(45deg)">🏔️↘️</div>';
                        } elseif ($v == 'css_debris_flow') {
                            echo '<div class="icon-large">🌊🪨</div>';
                        } elseif ($v == 'css_quake_safe') {
                            echo '<div class="icon-large">🙆‍♂️📐</div>';
                        } elseif ($v == 'css_warning') {
                            echo '<div class="icon-large" style="color:red;animation:pulse 0.5s infinite">🚨</div>';
                        } elseif ($v == 'css_rainforest') {
                            echo '<div class="icon-large">🌴</div>';
                        } elseif ($v == 'css_cactus') {
                            echo '<div class="icon-large">🌵</div>';
                        } elseif ($v == 'css_black_soil') {
                            echo '<div style="width:100px;height:100px;background:#212121;border-radius:50%;box-shadow:0 0 20px #000;"></div>';
                        
                        // 外部图片
                        } elseif (strpos($v, 'http') === 0) {
                            echo "<img src='$v' class='web-img'>";
                        
                        // 普通图标映射
                        } elseif (array_key_exists($v, $icon_map)) {
                            echo '<div class="icon-large">' . $icon_map[$v] . '</div>';
                        
                        // 兜底图标
                        } else {
                            echo '<div class="icon-large">🖼️</div>';
                        }

                    // CSS 渲染
                    if ($v == 'css_rotation') echo '<div class="css_rotation"></div>';
                    elseif ($v == 'css_day_night') echo '<div class="css_day_night"></div>';
                    elseif ($v == 'css_orbit') echo '<div class="css_orbit"><div class="css_sun"></div><div class="css_earth_moving"></div></div>';
                    elseif ($v == 'css_sun_move') echo '<div class="css_sun_move"><div class="line-n">23°26′N</div><div class="line-s">23°26′S</div><div class="sun-point"></div></div>';
                    elseif ($v == 'css_clock') echo '<div class="icon-large">⏰</div>';
                    elseif ($v == 'css_speed_map') echo '<div class="icon-large">🏎️</div>';
                    elseif ($v == 'css_perihelion') echo '<div class="icon-large">☀️🏃</div>';
                    elseif ($v == 'css_angle') echo '<div class="icon-large">📐</div>';
                    elseif ($v == 'css_date_line') echo '<div class="icon-large">📅</div>';
                    elseif ($v == 'css_coriolis') echo '<div class="icon-large">🌀</div>';
                    elseif ($v == 'css_day_length') echo '<div class="icon-large">🌗</div>';
                    elseif ($v == 'css_sun_height') echo '<div class="icon-large">📏</div>';
                    
                    // 默认图标
                    elseif (strpos($v, 'icon_') === 0) {
                        $map = ['icon_earth'=>'🌍','icon_star'=>'⭐','icon_final'=>'🎓','icon_plane'=>'✈️','icon_chart'=>'📊','icon_water_drop'=>'💧','icon_shield'=>'🛡️','icon_compare'=>'🆚','icon_storm'=>'🌪️','icon_alert_red'=>'🚨','icon_life'=>'🧬','icon_tree'=>'🌳','icon_balance'=>'⚖️','icon_map_scatter'=>'🗺️','icon_rebuild'=>'🏗️','icon_drill'=>'📢','icon_money'=>'💰','icon_badge'=>'👮','icon_ship'=>'🚢','icon_factory'=>'🏭','icon_phone'=>'📱','icon_train'=>'🚄','icon_fire'=>'🔥','icon_key'=>'🔑','icon_drone'=>'🚁','icon_baby'=>'👶','icon_hu_line'=>'🇨🇳','icon_leaf_shiny'=>'🍃','icon_park'=>'🏡','icon_forest_map'=>'🗺️','icon_soil_layers'=>'🥪','icon_soil_comp'=>'🧪','icon_shovel'=>'⛏️','icon_rock_plant'=>'🪨','icon_climate_soil'=>'🌦️','icon_protect_soil'=>'🛡️','icon_salt'=>'🧂','icon_drought'=>'☀️','icon_china_map'=>'🇨🇳','icon_cold'=>'🥶','icon_arrow_map'=>'↘️','icon_sandstorm'=>'🏜️','icon_earth_crack'=>'🏚️','icon_chain'=>'🔗','icon_alert_yellow'=>'⚠️','icon_radar'=>'📡','icon_flood_safe'=>'🏊','icon_run_direction'=>'🏃','icon_kit'=>'⛑️','icon_siren'=>'🚑','icon_satellite'=>'🛰️','icon_beidou'=>'🌌','icon_integration'=>'🧩','icon_map_scatter'=>'🗺️', 'icon_chart'=>'📊', 'icon_hu_line'=>'📉', 
                            'icon_integration'=>'🧩', 'icon_climate_soil'=>'🌦️', 'icon_factory'=>'🏭', 
                            'icon_final'=>'🎓', 'icon_chain'=>'🔗', 'icon_park'=>'🏡', 
                            'icon_compare'=>'🆚', 'icon_life'=>'🐟', 'icon_earth'=>'🌍'];
                        echo '<div class="icon-large">'.($map[$v]??'🖼️').'</div>';
                    }

                    ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="nav-bar d-flex justify-content-between align-items-center">
        <button class="btn btn-secondary rounded-pill px-3 flex-grow-1 mx-1" onclick="move(-1)">上一页</button>
        <span class="mx-2 fw-bold text-muted small" id="pg-num" style="white-space:nowrap">1 / <?php echo count($slides); ?></span>
        <button class="btn btn-primary rounded-pill px-3 flex-grow-1 mx-1 text-truncate" id="next-btn" onclick="move(1)">下一页</button>
    </div>
    <script>
        let cur = 0; const total = <?php echo count($slides); ?>;
        const currentId = '<?php echo $id; ?>';
        function show(idx) {
            document.querySelectorAll('.slide-card').forEach(el => el.classList.remove('active'));
            document.getElementById('slide-' + idx).classList.add('active');
            cur = idx; document.getElementById('pg-num').innerText = (cur + 1) + " / " + total;
            const btn = document.getElementById('next-btn');
            if(cur === total - 1) { 
                btn.innerText = "进入考核 📝"; btn.classList.replace('btn-primary', 'btn-success'); 
                btn.onclick = () => window.location.href = 'quiz.php?id=' + currentId; 
            } else { 
                btn.innerText = "下一页"; btn.classList.replace('btn-success', 'btn-primary'); 
                btn.onclick = () => move(1); 
            }
        }
        function move(dir) { if(cur + dir >= 0 && cur + dir < total) show(cur + dir); }
        window.onload = function() { show(0); };
    </script>
</body>
</html>