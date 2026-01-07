<?php include 'data_zoo.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>地理乐 Geo_Fun</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif; }
        
        /* 英雄横幅：支持哪吒主题色渐变 */
        .hero { 
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%); 
            color: white; 
            padding: 60px 20px; 
            border-radius: 0 0 50% 50% / 30px; 
            margin-bottom: 40px; 
            transition: all 0.5s ease;
        }
        
        .book-card { 
            border: none; 
            border-radius: 15px; 
            background: white; 
            transition: 0.3s; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            overflow: hidden; /* 防止bar溢出圆角 */
        }
        
        .book-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.1); 
        }
        
        .bar { height: 8px; width: 100%; }
        
        /* 移动端适配 */
        @media (max-width: 576px) {
            .hero h1 { font-size: 2rem; }
            .hero { padding: 40px 15px; border-radius: 0 0 20px 20px; }
            .book-card { margin-bottom: 15px; }
        }
    </style>
</head>
<body>
    <div class="hero text-center">
        <h1 class="display-4 fw-bold">🌏 地理乐 Geo_Fun </h1>
        <p class="lead mt-3">疯狂动物城 & 哪吒传奇 · 动物警校地理培训基地</p>
    </div>
    
    <div class="container mb-5">
        <div class="row g-4 justify-content-center">
            <?php foreach ($books as $id => $book): ?>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card book-card h-100">
                    <div class="bar" style="background: <?php echo $book['color']; ?>"></div>
                    
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div style="font-size: 3.5rem; margin: 15px 0;"><?php echo $book['icon']; ?></div>
                        <h4 class="fw-bold mb-2"><?php echo $book['title']; ?></h4>
                        <p class="text-muted small mb-4 flex-grow-1"><?php echo $book['desc']; ?></p>
                        
                        <?php if($book['status'] == 'active'): ?>
                            <a href="chapter_map.php?bid=<?php echo $id; ?>" 
                               class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm" 
                               style="background: <?php echo $book['color']; ?>; border:none;">
                               开始学习
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light rounded-pill w-100 py-2 text-muted" disabled>
                                🔒 待解锁
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center text-muted mt-5 mb-3">
            <small>© 2026 Geo_Fun 动物警校&陈堂关地理培训基地</small>
        </div>
    </div>
</body>
</html>