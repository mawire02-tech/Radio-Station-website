<?php
// ── PresenterModel ───────────────────────────────────────────
class PresenterModel extends BaseModel
{
    public function getActive(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, GROUP_CONCAT(s.title SEPARATOR ', ') AS show_titles
             FROM presenters p
             LEFT JOIN shows s ON s.presenter_id = p.id AND s.is_active = 1
             WHERE p.is_active = 1
             GROUP BY p.id
             ORDER BY p.sort_order, p.name"
        );
    }

    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT p.*, (SELECT COUNT(*) FROM shows s WHERE s.presenter_id = p.id) AS show_count
             FROM presenters p ORDER BY p.sort_order, p.name LIMIT $perPage OFFSET $offset"
        );
    }

    public function countAll(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM presenters")['cnt'] ?? 0);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM presenters WHERE id = ?", [$id]);
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT p.*, GROUP_CONCAT(s.title SEPARATOR '||') AS show_titles
             FROM presenters p LEFT JOIN shows s ON s.presenter_id = p.id
             WHERE p.slug = ? AND p.is_active = 1 GROUP BY p.id",
            [$slug]
        );
    }

    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO presenters (name,slug,bio,photo,role,email,twitter,instagram,facebook,sort_order)
             VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$d['name'],$d['slug'],$d['bio'],$d['photo']??null,$d['role'],$d['email']??null,
             $d['twitter']??null,$d['instagram']??null,$d['facebook']??null,$d['sort_order']??0]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        return $this->db->execute(
            "UPDATE presenters SET name=?,slug=?,bio=?,photo=?,role=?,email=?,twitter=?,instagram=?,facebook=?,sort_order=?,is_active=?
             WHERE id=?",
            [$d['name'],$d['slug'],$d['bio'],$d['photo']??null,$d['role'],$d['email']??null,
             $d['twitter']??null,$d['instagram']??null,$d['facebook']??null,
             $d['sort_order']??0,$d['is_active']??1,$id]
        ) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM presenters WHERE id = ?", [$id]) > 0;
    }
}

// ── ShowModel ────────────────────────────────────────────────
class ShowModel extends BaseModel
{
    public function getActive(): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, p.name AS presenter_name, g.name AS genre_name
             FROM shows s
             JOIN presenters p ON p.id = s.presenter_id
             JOIN genres g     ON g.id = s.genre_id
             WHERE s.is_active = 1
             ORDER BY s.title"
        );
    }

    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT s.*, p.name AS presenter_name, g.name AS genre_name
             FROM shows s JOIN presenters p ON p.id = s.presenter_id JOIN genres g ON g.id = s.genre_id
             ORDER BY s.title LIMIT $perPage OFFSET $offset"
        );
    }

    public function countAll(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM shows")['cnt'] ?? 0);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT s.*, p.name AS presenter_name, g.name AS genre_name
             FROM shows s JOIN presenters p ON p.id = s.presenter_id JOIN genres g ON g.id = s.genre_id
             WHERE s.id = ?",
            [$id]
        );
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT s.*, p.name AS presenter_name, p.slug AS presenter_slug, g.name AS genre_name
             FROM shows s JOIN presenters p ON p.id = s.presenter_id JOIN genres g ON g.id = s.genre_id
             WHERE s.slug = ? AND s.is_active = 1",
            [$slug]
        );
    }

    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO shows (presenter_id,genre_id,title,slug,description,image) VALUES (?,?,?,?,?,?)",
            [$d['presenter_id'],$d['genre_id'],$d['title'],$d['slug'],$d['description'],$d['image']??null]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        return $this->db->execute(
            "UPDATE shows SET presenter_id=?,genre_id=?,title=?,slug=?,description=?,image=?,is_active=? WHERE id=?",
            [$d['presenter_id'],$d['genre_id'],$d['title'],$d['slug'],$d['description'],$d['image']??null,$d['is_active']??1,$id]
        ) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM shows WHERE id = ?", [$id]) > 0;
    }

    public function getGenres(): array
    {
        return $this->db->fetchAll("SELECT * FROM genres ORDER BY name");
    }
}

// ── ScheduleModel ────────────────────────────────────────────
class ScheduleModel extends BaseModel
{
    public function getByDay(int $day): array
    {
        return $this->db->fetchAll(
            "SELECT sc.*, s.title AS show_title, s.slug AS show_slug,
                    p.name AS presenter_name, g.name AS genre_name
             FROM schedule sc
             JOIN shows s     ON s.id = sc.show_id
             JOIN presenters p ON p.id = s.presenter_id
             JOIN genres g     ON g.id = s.genre_id
             WHERE sc.day_of_week = ? AND s.is_active = 1
             ORDER BY sc.start_time",
            [$day]
        );
    }

    public function getWeekly(): array
    {
        $week = [];
        for ($d = 0; $d <= 6; $d++) {
            $week[$d] = $this->getByDay($d);
        }
        return $week;
    }

    public function create(array $d): bool
    {
        $this->db->execute(
            "INSERT INTO schedule (show_id,day_of_week,start_time,end_time,is_repeat) VALUES (?,?,?,?,?)",
            [$d['show_id'],$d['day_of_week'],$d['start_time'],$d['end_time'],$d['is_repeat']??0]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM schedule WHERE id = ?", [$id]) > 0;
    }
}

// ── PodcastModel ─────────────────────────────────────────────
class PodcastModel extends BaseModel
{
    public function getRecent(int $limit = 6): array
    {
        return $this->db->fetchAll(
            "SELECT pod.*, s.title AS show_title, s.slug AS show_slug, p.name AS presenter_name
             FROM podcasts pod JOIN shows s ON s.id = pod.show_id JOIN presenters p ON p.id = s.presenter_id
             ORDER BY pod.published_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getByShow(int $showId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM podcasts WHERE show_id = ? ORDER BY published_at DESC",
            [$showId]
        );
    }

    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT pod.*, s.title AS show_title
             FROM podcasts pod JOIN shows s ON s.id = pod.show_id
             ORDER BY pod.published_at DESC LIMIT $perPage OFFSET $offset"
        );
    }

    public function countAll(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM podcasts")['cnt'] ?? 0);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM podcasts WHERE id = ?", [$id]);
    }

    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO podcasts (show_id,title,description,audio_file,duration,file_size,published_at)
             VALUES (?,?,?,?,?,?,?)",
            [$d['show_id'],$d['title'],$d['description']??null,$d['audio_file'],
             $d['duration']??0,$d['file_size']??0,$d['published_at']??date('Y-m-d H:i:s')]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        return $this->db->execute(
            "UPDATE podcasts SET show_id=?,title=?,description=?,audio_file=?,duration=?,published_at=? WHERE id=?",
            [$d['show_id'],$d['title'],$d['description']??null,$d['audio_file'],$d['duration']??0,$d['published_at'],$id]
        ) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM podcasts WHERE id = ?", [$id]) > 0;
    }
}

// ── RequestModel ─────────────────────────────────────────────
class RequestModel extends BaseModel
{
    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO music_requests (listener_name,listener_email,song_title,artist,shoutout,show_id,ip_address)
             VALUES (?,?,?,?,?,?,?)",
            [$d['listener_name'],$d['listener_email']??null,$d['song_title'],
             $d['artist'],$d['shoutout']??null,$d['show_id']??null,$d['ip_address']]
        );
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE, string $status = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = $status ? 'WHERE r.status = ?' : '';
        $params = $status ? [$status] : [];
        return $this->db->fetchAll(
            "SELECT r.*, s.title AS show_title FROM music_requests r
             LEFT JOIN shows s ON s.id = r.show_id
             $where ORDER BY r.created_at DESC LIMIT $perPage OFFSET $offset",
            $params
        );
    }

    public function countAll(string $status = ''): int
    {
        $where = $status ? 'WHERE status = ?' : '';
        $row = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM music_requests $where", $status ? [$status] : []);
        return (int)($row['cnt'] ?? 0);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->db->execute("UPDATE music_requests SET status=? WHERE id=?", [$status, $id]) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM music_requests WHERE id = ?", [$id]) > 0;
    }

    public function checkDuplicate(string $ip, string $song, string $artist): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM music_requests
             WHERE ip_address=? AND song_title=? AND artist=? AND created_at > DATE_SUB(NOW(),INTERVAL 1 HOUR)",
            [$ip, $song, $artist]
        );
        return $row !== null;
    }
}

// ── FeedbackModel ────────────────────────────────────────────
class FeedbackModel extends BaseModel
{
    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO feedback (name,email,subject,message,type,ip_address) VALUES (?,?,?,?,?,?)",
            [$d['name'],$d['email']??null,$d['subject'],$d['message'],$d['type']??'general',$d['ip_address']]
        );
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT * FROM feedback ORDER BY created_at DESC LIMIT $perPage OFFSET $offset"
        );
    }

    public function countAll(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM feedback")['cnt'] ?? 0);
    }

    public function markRead(int $id): void
    {
        $this->db->execute("UPDATE feedback SET is_read=1 WHERE id=?", [$id]);
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM feedback WHERE id=?", [$id]) > 0;
    }

    public function countUnread(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM feedback WHERE is_read=0")['cnt'] ?? 0);
    }
}

// ── PollModel ────────────────────────────────────────────────
class PollModel extends BaseModel
{
    public function getResults(): array
    {
        return $this->db->fetchAll(
            "SELECT g.id, g.name, COUNT(v.id) AS votes
             FROM genres g LEFT JOIN poll_votes v ON v.genre_id = g.id
             GROUP BY g.id ORDER BY votes DESC"
        );
    }

    public function vote(int $genreId, string $ip): bool
    {
        try {
            $this->db->execute(
                "INSERT INTO poll_votes (genre_id, ip_address) VALUES (?,?)",
                [$genreId, $ip]
            );
            return true;
        } catch (Exception $e) {
            return false; // Duplicate vote
        }
    }

    public function hasVoted(string $ip): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM poll_votes WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)",
            [$ip]
        );
        return $row !== null;
    }
}

// ── EventModel ───────────────────────────────────────────────
class EventModel extends BaseModel
{
    public function getUpcoming(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT ?",
            [$limit]
        );
    }

    public function getByMonth(int $year, int $month): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM events WHERE YEAR(event_date)=? AND MONTH(event_date)=? ORDER BY event_date",
            [$year, $month]
        );
    }

    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM events";
        $params = [];
        
        if ($search) {
            $query .= " WHERE title LIKE ?";
            $params[] = "%$search%";
        }
        
        $query .= " ORDER BY event_date DESC LIMIT $perPage OFFSET $offset";
        return $this->db->fetchAll($query, $params);
    }

    public function countAll(?string $search = null): int
    {
        $query = "SELECT COUNT(*) AS cnt FROM events";
        $params = [];
        
        if ($search) {
            $query .= " WHERE title LIKE ?";
            $params[] = "%$search%";
        }
        
        $result = $this->db->fetchOne($query, $params);
        return (int)($result['cnt'] ?? 0);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM events WHERE id=?", [$id]);
    }

    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO events (title,description,location,event_date,start_time,end_time,is_featured,created_by)
             VALUES (?,?,?,?,?,?,?,?)",
            [$d['title'],$d['description']??null,$d['location']??null,$d['event_date'],
             $d['start_time']??null,$d['end_time']??null,$d['is_featured']??0,$d['created_by']]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        return $this->db->execute(
            "UPDATE events SET title=?,description=?,location=?,event_date=?,start_time=?,end_time=?,is_featured=? WHERE id=?",
            [$d['title'],$d['description']??null,$d['location']??null,$d['event_date'],
             $d['start_time']??null,$d['end_time']??null,$d['is_featured']??0,$id]
        ) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM events WHERE id=?", [$id]) > 0;
    }
}

// ── SettingsModel ────────────────────────────────────────────
class SettingsModel extends BaseModel
{
    private static array $cache = [];

    public function get(string $key, string $default = ''): string
    {
        if (!isset(self::$cache[$key])) {
            $row = $this->db->fetchOne("SELECT setting_val FROM settings WHERE setting_key=?", [$key]);
            self::$cache[$key] = $row ? $row['setting_val'] : $default;
        }
        return self::$cache[$key];
    }

    public function getAll(): array
    {
        $rows = $this->db->fetchAll("SELECT * FROM settings ORDER BY group_name, setting_key");
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['group_name']][] = $row;
        }
        return $grouped;
    }

    public function set(string $key, string $value): void
    {
        $this->db->execute(
            "INSERT INTO settings (setting_key,setting_val,label) VALUES (?,?,'') ON DUPLICATE KEY UPDATE setting_val=?",
            [$key, $value, $value]
        );
        self::$cache[$key] = $value;
    }

    public function bulkUpdate(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}

// ── UserModel ────────────────────────────────────────────────
class UserModel extends BaseModel
{
    public function getByUsername(string $username): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE username=? AND is_active=1",
            [$username]
        );
    }

    public function getByEmail(string $email): ?array
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE email=?", [$email]);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE id=?", [$id]);
    }

    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT id,username,email,full_name,role,is_active,last_login,created_at FROM users ORDER BY created_at DESC LIMIT $perPage OFFSET $offset"
        );
    }

    public function countAll(): int
    {
        return (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM users")['cnt'] ?? 0);
    }

    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO users (username,email,password,full_name,role) VALUES (?,?,?,?,?)",
            [$d['username'],$d['email'],Security::hashPassword($d['password']),$d['full_name'],$d['role']??'editor']
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $sets = "username=?,email=?,full_name=?,role=?,is_active=?";
        $params = [$d['username'],$d['email'],$d['full_name'],$d['role'],$d['is_active']??1];
        if (!empty($d['password'])) {
            $sets .= ",password=?";
            $params[] = Security::hashPassword($d['password']);
        }
        $params[] = $id;
        return $this->db->execute("UPDATE users SET $sets WHERE id=?", $params) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM users WHERE id=? AND role != 'superadmin'", [$id]) > 0;
    }

    public function updateLastLogin(int $id): void
    {
        $this->db->execute("UPDATE users SET last_login=NOW() WHERE id=?", [$id]);
    }

    public function logActivity(int $userId, string $action, string $entity = '', int $entityId = 0, string $detail = ''): void
    {
        $this->db->execute(
            "INSERT INTO activity_log (user_id,action,entity,entity_id,detail,ip_address) VALUES (?,?,?,?,?,?)",
            [$userId, $action, $entity, $entityId, $detail, Security::getIp()]
        );
    }
}

// ── NewsModel (merged) ────────────────────────────────────────
class NewsModel extends BaseModel
{
    public function getPublished(int $page=1,int $perPage=NEWS_PER_PAGE,?int $catId=null,?string $search=null): array
    {
        $offset=($page-1)*$perPage; $where=['n.status = ?']; $params=['published'];
        if($catId){$where[]='n.category_id = ?';$params[]=$catId;}
        if($search){$where[]='(n.title LIKE ? OR n.excerpt LIKE ?)';$params[]="%$search%";$params[]="%$search%";}
        $w='WHERE '.implode(' AND ',$where);
        return $this->db->fetchAll("SELECT n.*,c.name AS category_name,c.slug AS category_slug,c.color AS category_color,u.full_name AS author_name FROM news n JOIN categories c ON c.id=n.category_id JOIN users u ON u.id=n.author_id $w ORDER BY n.published_at DESC LIMIT $perPage OFFSET $offset",$params);
    }
    public function countPublished(?int $catId=null,?string $search=null): int
    {
        $where=['n.status = ?'];$params=['published'];
        if($catId){$where[]='n.category_id = ?';$params[]=$catId;}
        if($search){$where[]='(n.title LIKE ? OR n.excerpt LIKE ?)';$params[]="%$search%";$params[]="%$search%";}
        $w='WHERE '.implode(' AND ',$where);
        return(int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM news n $w",$params)['cnt']??0);
    }
    public function getFeatured(int $limit=3): array
    {
        return $this->db->fetchAll("SELECT n.*,c.name AS category_name,c.color AS category_color FROM news n JOIN categories c ON c.id=n.category_id WHERE n.status='published' AND n.featured=1 ORDER BY n.published_at DESC LIMIT ?",[$limit]);
    }
    public function getBySlug(string $slug): ?array
    {
        $row=$this->db->fetchOne("SELECT n.*,c.name AS category_name,c.color AS category_color,u.full_name AS author_name FROM news n JOIN categories c ON c.id=n.category_id JOIN users u ON u.id=n.author_id WHERE n.slug=? AND n.status='published'",[$slug]);
        if($row){$this->db->execute("UPDATE news SET views=views+1 WHERE id=?",[$row['id']]);}
        return $row;
    }
    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT n.*,c.name AS category_name FROM news n JOIN categories c ON c.id=n.category_id WHERE n.id=?",[$id]);
    }
    public function getAll(int $page=1,int $perPage=ADMIN_PER_PAGE,?string $search=null): array
    {
        $offset=($page-1)*$perPage;$where=[];$params=[];
        if($search){$where[]='(n.title LIKE ?)';$params[]="%$search%";}
        $w=$where?'WHERE '.implode(' AND ',$where):'';
        return $this->db->fetchAll("SELECT n.*,c.name AS category_name,u.full_name AS author_name FROM news n JOIN categories c ON c.id=n.category_id JOIN users u ON u.id=n.author_id $w ORDER BY n.created_at DESC LIMIT $perPage OFFSET $offset",$params);
    }
    public function countAll(?string $search=null): int
    {
        $where=[];$params=[];
        if($search){$where[]='(n.title LIKE ?)';$params[]="%$search%";}
        $w=$where?'WHERE '.implode(' AND ',$where):'';
        return(int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM news n $w",$params)['cnt']??0);
    }
    public function create(array $d): int
    {
        $this->db->execute("INSERT INTO news(category_id,author_id,title,slug,excerpt,body,image,status,featured,published_at)VALUES(?,?,?,?,?,?,?,?,?,?)",[$d['category_id'],$d['author_id'],$d['title'],$d['slug'],$d['excerpt'],$d['body'],$d['image']??null,$d['status'],$d['featured']??0,$d['status']==='published'?date('Y-m-d H:i:s'):null]);
        return(int)$this->db->lastInsertId();
    }
    public function update(int $id,array $d): bool
    {
        $pa=null;if($d['status']==='published'){$e=$this->getById($id);$pa=$e['published_at']??date('Y-m-d H:i:s');}
        return $this->db->execute("UPDATE news SET category_id=?,title=?,slug=?,excerpt=?,body=?,image=?,status=?,featured=?,published_at=? WHERE id=?",[$d['category_id'],$d['title'],$d['slug'],$d['excerpt'],$d['body'],$d['image']??null,$d['status'],$d['featured']??0,$pa,$id])>0;
    }
    public function delete(int $id): bool{return $this->db->execute("DELETE FROM news WHERE id=?",[$id])>0;}
    public function getCategories(): array{return $this->db->fetchAll("SELECT * FROM categories ORDER BY name");}
    public function getArchiveMonths(): array
    {
        return $this->db->fetchAll("SELECT DATE_FORMAT(published_at,'%Y-%m') AS ym,DATE_FORMAT(published_at,'%M %Y') AS label,COUNT(*) AS cnt FROM news WHERE status='published' GROUP BY ym ORDER BY ym DESC LIMIT 24");
    }
}

// ── ListenerAnalyticsModel ───────────────────────────────────
class ListenerAnalyticsModel extends BaseModel
{
    public function recordListener(string $sessionId, string $ipAddress, ?string $page = null): bool
    {
        $userAgent = Security::sanitize($_SERVER['HTTP_USER_AGENT'] ?? '');
        return $this->db->execute(
            "INSERT INTO listener_analytics (session_id, ip_address, user_agent, page)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE last_active = CURRENT_TIMESTAMP, page = ?",
            [$sessionId, $ipAddress, $userAgent, $page, $page]
        ) > 0;
    }

    public function getLiveListenerCount(): int
    {
        // Count unique sessions active in the last 5 minutes
        $result = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT session_id) AS cnt FROM listener_analytics 
             WHERE last_active >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)"
        );
        return (int)($result['cnt'] ?? 0);
    }

    public function getDailyListenerCount(): int
    {
        // Count unique sessions from today
        $result = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT session_id) AS cnt FROM listener_analytics 
             WHERE DATE(timestamp) = CURDATE()"
        );
        return (int)($result['cnt'] ?? 0);
    }

    public function getTotalSessionCount(): int
    {
        // Count total unique sessions
        $result = $this->db->fetchOne("SELECT COUNT(DISTINCT session_id) AS cnt FROM listener_analytics");
        return (int)($result['cnt'] ?? 0);
    }

    public function cleanup(): bool
    {
        // Delete sessions older than 30 days
        return $this->db->execute(
            "DELETE FROM listener_analytics WHERE timestamp < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        ) > 0;
    }
}

// ── SubscriberModel ─────────────────────────────────────────
class SubscriberModel extends BaseModel
{
    public function getAll(int $page = 1, int $perPage = ADMIN_PER_PAGE, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM subscribers WHERE is_active = 1";
        $params = [];
        
        if ($search) {
            $query .= " AND (email LIKE ? OR name LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        
        $query .= " ORDER BY subscribed_at DESC LIMIT $perPage OFFSET $offset";
        return $this->db->fetchAll($query, $params);
    }

    public function countAll(?string $search = null): int
    {
        $query = "SELECT COUNT(*) AS cnt FROM subscribers WHERE is_active = 1";
        $params = [];
        
        if ($search) {
            $query .= " AND (email LIKE ? OR name LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        
        return (int)($this->db->fetchOne($query, $params)['cnt'] ?? 0);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM subscribers WHERE id = ?", [$id]);
    }

    public function getByEmail(string $email): ?array
    {
        return $this->db->fetchOne("SELECT * FROM subscribers WHERE email = ?", [$email]);
    }

    public function create(array $d): int
    {
        $this->db->execute(
            "INSERT INTO subscribers (email, name, is_active) VALUES (?, ?, ?)",
            [$d['email'], $d['name'] ?? null, 1]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        return $this->db->execute(
            "UPDATE subscribers SET name = ?, is_active = ? WHERE id = ?",
            [$d['name'] ?? null, $d['is_active'] ?? 1, $id]
        ) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("UPDATE subscribers SET is_active = 0, unsubscribed_at = NOW() WHERE id = ?", [$id]) > 0;
    }

    public function getActiveCount(): int
    {
        $result = $this->db->fetchOne("SELECT COUNT(*) AS cnt FROM subscribers WHERE is_active = 1");
        return (int)($result['cnt'] ?? 0);
    }
}
