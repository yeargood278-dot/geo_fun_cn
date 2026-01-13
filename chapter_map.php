<?php include 'data_zoo.php'; 
$bid = $_GET['bid'] ?? 'b1';

// 选择导图数据 (系统修复部分)
if ($bid == 'b2') $current_mindmap = $b2_mindmap;
elseif ($bid == 'xb1') $current_mindmap = $xb1_mindmap;
elseif ($bid == 'xb2') $current_mindmap = $xb2_mindmap;
elseif ($bid == 'xb3') $current_mindmap = $xb3_mindmap; // 修复点：加载选必3导图
else $current_mindmap = $b1_mindmap;

$current_title = $books[$bid]['title'];
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"> 
    <title>作战地图 - <?php echo $current_title; ?></title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcdn.net/ajax/libs/mermaid/10.9.0/mermaid.min.js"></script>
    <style>
        body { background: #eef2f3; height: 100vh; height: 100dvh; display: flex; flex-direction: column; overflow: hidden; }
        .navbar { background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); z-index: 10; padding: 10px 20px; flex-shrink: 0; }
        
        /* 地图视口：支持触摸滚动 */
        .map-viewport { 
            flex: 1; 
            width: 100%; 
            overflow: auto; /* 允许滚动 */
            -webkit-overflow-scrolling: touch; /* iOS流畅滚动 */
            display: flex; 
            justify-content: center; /* 居中显示 */
            align-items: flex-start; /* 顶部对齐，防止长图被截断 */
            padding: 20px; 
            background-color: #f8f9fa; 
            background-image: radial-gradient(#dee2e6 1px, transparent 1px); 
            background-size: 20px 20px; 
        }

        /* Mermaid 容器适配 */
        .mermaid {
            /* 必修二横向内容多，设置最小宽度触发横向滚动，防止手机上挤压变形 */
            <?php if($bid == 'b2'): ?>
            min-width: 900px; 
            <?php else: ?>
            width: 100%;
            max-width: 1200px;
            min-width: 300px;
            <?php endif; ?>
        }

        /* 节点样式增强 */
        g.node rect, g.node circle, g.node polygon {
            stroke-width: 2px !important; cursor: pointer !important; 
            transition: all 0.2s ease !important;
            filter: drop-shadow(3px 3px 0px rgba(0,0,0,0.1)) !important;
        }
        g.node:hover rect {
            transform: scale(1.05); filter: drop-shadow(5px 5px 2px rgba(0,0,0,0.2)) !important;
        }
        g.node:active rect {
            transform: scale(0.95);
        }
        /* 强制文字颜色 */
        g.node .label { color: white !important; font-family: 'Microsoft YaHei', sans-serif; }
        
        /* 哪吒主题色适配 */
        <?php if($bid == 'b2'): ?>
        .navbar-brand { color: #d35400 !important; }
        <?php endif; ?>
    </style>
</head>
<body>
<nav class="navbar navbar-light bg-white px-3 shadow-sm justify-content-between">
        <span class="navbar-brand fw-bold"><?php echo $current_title; ?></span>
        <div class="nav-links d-flex align-items-center">
            <a href="chapter_map.php?bid=b1" class="text-primary text-decoration-none">必1</a>
            <a href="chapter_map.php?bid=b2" class="text-warning text-decoration-none">必2</a>
            <a href="chapter_map.php?bid=xb1" class="text-success text-decoration-none">选1</a>
            <a href="chapter_map.php?bid=xb2" class="text-secondary text-decoration-none" style="color:#9b59b6!important;">选2</a>
            <a href="chapter_map.php?bid=xb3" class="text-danger text-decoration-none">选3</a> <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill ms-2">🏠</a>
        </div>
    </nav>
    <div class="map-viewport">
        <div class="mermaid">
            <?php echo $current_mindmap; ?>
        </div>
    </div>
    <script>
        // 初始化 mermaid
        mermaid.initialize({ 
            startOnLoad: true, 
            theme: 'base', 
            securityLevel: 'loose', 
            flowchart: { 
                // 必修二设为false允许溢出滚动，保持原始比例；必修一设为true自适应
                useMaxWidth: <?php echo ($bid == 'b2') ? 'false' : 'true'; ?>, 
                htmlLabels: true, 
                curve: 'basis' 
            } 
        });
    </script>
</body>
</html>
