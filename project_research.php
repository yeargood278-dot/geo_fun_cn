<?php 
include 'data_zoo.php'; 

// 获取书籍ID，默认为 b1
$bid = $_GET['bid'] ?? 'b1';

// 根据 bid 加载对应的项目列表
if ($bid == 'b2') {
    $current_projects = $research_projects_b2;
    $theme_color = '#d35400'; // 哪吒红/橙
    $title_prefix = '🔥 必修二';
    $back_link = 'chapter_map.php?bid=b2'; // 返回必修二地图
} else {
    $current_projects = $research_projects_b1;
    $theme_color = '#3498db'; // 必修一蓝
    $title_prefix = '🌍 必修一';
    $back_link = 'chapter_map.php?bid=b1'; // 返回必修一地图
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>问题研究 - <?php echo $title_prefix; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif; padding-bottom: 60px; }
        .header-section { 
            background: linear-gradient(135deg, <?php echo $theme_color; ?> 0%, #2c3e50 100%); 
            color: white; padding: 60px 20px; text-align: center; border-radius: 0 0 30px 30px; margin-bottom: 40px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
        }
        .project-card { border: none; border-radius: 15px; margin-bottom: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: all 0.3s ease; }
        .project-card:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .accordion-button { background-color: white; color: #2c3e50; font-weight: bold; font-size: 1.1rem; padding: 20px; border: none; }
        .accordion-button:not(.collapsed) { background-color: #f8f9fa; color: <?php echo $theme_color; ?>; box-shadow: inset 0 -1px 0 rgba(0,0,0,.125); }
        .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,0.1); }
        .icon-box { font-size: 1.5rem; margin-right: 15px; width: 40px; text-align: center; }
        .content-box { background: #fff; padding: 25px; border-top: 3px solid <?php echo $theme_color; ?>; }
        .label-tag { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; margin-bottom: 10px; }
        .tag-point { background: #e8f6f3; color: #1abc9c; }
        .tag-value { background: #fef5e7; color: #f39c12; }
    </style>
</head>
<body>

    <div class="header-section animate__animated animate__fadeInDown">
        <h1 class="display-5 fw-bold mb-3"><?php echo $title_prefix; ?> 问题研究</h1>
        <p class="lead opacity-75">打破章节壁垒 · 融合全书知识 · 解决现实问题</p>
    </div>

    <div class="container" style="max-width: 900px;">
        <div class="accordion" id="projectAccordion">
            <?php foreach($current_projects as $index => $proj): ?>
            <div class="accordion-item project-card animate__animated animate__fadeInUp" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                        <span class="icon-box"><?php echo $proj['icon']; ?></span>
                        <?php echo $proj['title']; ?>
                    </button>
                </h2>
                <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#projectAccordion">
                    <div class="accordion-body content-box">
                        <div class="mb-4">
                            <span class="label-tag tag-point">💡 主要观点与研究思路</span>
                            <p class="text-secondary lh-lg mb-0"><?php echo $proj['points']; ?></p>
                        </div>
                        <div>
                            <span class="label-tag tag-value">📈 研究趋势与价值</span>
                            <p class="text-secondary lh-lg mb-0"><?php echo $proj['value']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5 mb-5 text-muted">
            <small>✨ 点击标题展开详情，再次点击或点击其他项目自动收起 ✨</small>
        </div>

        <div class="text-center mb-5 pb-5">
            <a href="index.php" class="btn btn-primary rounded-pill px-4 mx-2 shadow">🏠 首页</a>
            <a href="<?php echo $back_link; ?>" class="btn btn-secondary rounded-pill px-4 mx-2 shadow">🗺️ 地图</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 如果 URL 中有 open 参数，自动展开对应项 (服务于地图页面点击进入)
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const openIdx = urlParams.get('open');
            if (openIdx !== null) {
                const target = document.getElementById('collapse' + openIdx);
                const btn = document.querySelector(`button[data-bs-target="#collapse${openIdx}"]`);
                if (target && btn) {
                    target.classList.add('show');
                    btn.classList.remove('collapsed');
                    btn.setAttribute('aria-expanded', 'true');
                    btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        };
    </script>
</body>
</html>
