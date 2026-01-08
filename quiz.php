<?php 
include 'data_zoo.php'; 
$id = $_GET['id'] ?? 'c1s1';
$content = $courses[$id] ?? $courses['c1s1'];
$qa = $content['quiz']['part_a'];
$qb = $content['quiz']['part_b'];

// 智能判断所属书籍
$bid = 'b1'; $theme_color = '#3498db'; // 默认蓝
if (strpos($id, 'b2') === 0) { $bid = 'b2'; $theme_color = '#d35400'; } // 必修二红
if (strpos($id, 'xb1') === 0) { $bid = 'xb1'; $theme_color = '#27ae60'; } // 选必一绿
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>特工考核</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header { background: <?php echo ($bid=='b2') ? '#d35400' : '#2c3e50'; ?>; color: white; padding: 40px 20px; border-radius: 0 0 20px 20px; text-align: center; }
        .q-item { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; border-left: 5px solid <?php echo ($bid=='b2') ? '#e67e22' : '#3498db'; ?>; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .ans-show { display: none; background: #d1e7dd; padding: 10px; margin-top: 10px; border-radius: 5px; color: #0f5132; word-break: break-word; }
        @media (max-width: 576px) {
            .header { padding: 30px 15px; }
            .header h2 { font-size: 1.5rem; }
            .container { padding-left: 15px; padding-right: 15px; }
            .btn-lg { width: 100%; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="header"><h2>📝 考核：<?php echo $content['title']; ?></h2></div>
    <div class="container my-5" style="max-width: 800px;">
        <h4 class="text-primary mb-3">Part A: 基础理论</h4>
        <form id="quizForm">
            <?php foreach($qa as $i => $q): ?>
            <div class="q-item" data-ans="<?php echo $q['ans']; ?>">
                <h5 class="fw-bold"><span class="badge <?php echo $q['type']=='single'?'bg-primary':'bg-success'; ?> me-2"><?php echo $q['type']=='single'?'单选':'多选'; ?></span><?php echo ($i+1) . '. ' . $q['q']; ?></h5>
                <div class="list-group mt-3">
                    <?php foreach($q['opt'] as $opt): ?>
                        <label class="list-group-item border-0">
                            <input class="form-check-input me-2" 
                                   type="<?php echo $q['type']=='single'?'radio':'checkbox'; ?>" 
                                   name="q_<?php echo $i; ?>" 
                                   value="<?php echo substr($opt,0,1); ?>">
                            <?php echo $opt; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="ans-show"><strong>✅ 正确答案：<?php echo $q['ans']; ?></strong><br><?php echo $q['expl']; ?></div>
            </div>
            <?php endforeach; ?>
            <div class="d-grid mb-5"><button type="button" class="btn btn-primary btn-lg shadow" onclick="check()">提交答卷</button></div>
        </form>
        <div id="partB" style="display: none;">
            <h4 class="text-danger mb-3">Part B: 逻辑推理</h4>
            <?php foreach($qb as $l): ?>
            <div class="card p-4 mb-3 border-danger shadow-sm">
                <h5><?php echo $l['title']; ?></h5>
                <p class="fw-bold mt-2"><?php echo $l['q']; ?></p>
                <button class="btn btn-sm btn-outline-danger" onclick="this.nextElementSibling.style.display='block'">查看分析</button>
                <div class="ans-show text-dark mt-3"><?php echo $l['ans']; ?></div>
            </div>
            <?php endforeach; ?>
            <div class="text-center mt-5 pb-5">
                <a href="index.php" class="btn btn-primary rounded-pill px-4 mx-2">🏠 首页</a>
                <a href="chapter_map.php?bid=<?php echo $bid; ?>" class="btn btn-secondary rounded-pill px-4 mx-2">🗺️ 地图</a>
            </div>
        </div>
    </div>
    <script>
        function check() {
            let score = 0;
            const total = document.querySelectorAll('.q-item').length;
            document.querySelectorAll('.q-item').forEach(el => {
                const correct = el.getAttribute('data-ans');
                const checked = Array.from(el.querySelectorAll('input:checked')).map(i=>i.value).sort().join('');
                
                const ansDiv = el.querySelector('.ans-show');
                ansDiv.style.display = 'block';
                
                if(checked === correct) { 
                    score++; 
                    el.style.borderLeftColor = 'green'; 
                } else { 
                    el.style.borderLeftColor = 'red'; 
                }
                el.querySelectorAll('input').forEach(i=>i.disabled=true);
            });
            alert('考核结束！得分：' + score + '/' + total);
            document.getElementById('partB').style.display = 'block';
            window.scrollTo(0, document.body.scrollHeight);
        }
    </script>
</body>
</html>