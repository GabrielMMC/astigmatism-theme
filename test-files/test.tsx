// test-theme.ts
import React, { useState, useEffect, FC } from 'react';

// Interfaces e tipos
interface User {
  id: number;
  name: string;
  email: string;
  status: 'active' | 'inactive';
}

type UserRole = 'admin' | 'editor' | 'viewer';

// Componente estilizado com props tipadas
const UserCard = styled.div<{ isActive: boolean }>`
  padding: 1.5rem;
  background: ${({ isActive }) => isActive ? '#20283A' : '#1A202E'};
  border-radius: 8px;
  color: #DBD5C0;
`;

const UserDashboard: FC<{ initialUsers: User[] }> = ({ initialUsers }) => {
  const [users, setUsers] = useState<User[]>(initialUsers);
  const [searchTerm, setSearchTerm] = useState<string>('');
  const [loading, setLoading] = useState<boolean>(false);

  const fetchUsers = async (): Promise<void> => {
    setLoading(true);
    try {
      await new Promise(resolve => setTimeout(resolve, 1000));
      setUsers(initialUsers);
    } catch {
      console.error('Error fetching users');
    } finally {
      setLoading(false);
    }
  };

  const filterUsers = <T extends User>(items: T[], term: string): T[] => {
    return items.filter(item =>
      item.name.toLowerCase().includes(term.toLowerCase())
    );
  };

  const renderUserRole = (role: UserRole): string => {
    switch(role) {
      case 'admin':
        return 'Administrador';
      case 'editor':
        return 'Editor';
      default:
        return 'Visualizador' ;
    }
  };

  useEffect(() => {
    function example() {
      if (true) { // Nível 1
        [1, 2, 3].forEach((item) => { // Nível 2
          if(true){
            if(true){
              if(true){
                if(true){
                  if(true){
                    if(true){
                      if(true){
                        if(true){
                          if(true){
                            if(true){
                              if(true){
                                if(true){
            
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
          console.log({ // Nível 3
            key: item
          });
        });
      }
    }

    if (searchTerm) {
      setUsers(filterUsers(initialUsers, searchTerm));
    } else {
      setUsers(initialUsers);
    }
  }, [searchTerm, initialUsers]);

  return (
    <div className="user-container">
      <div className="controls">
        <input
          type="text"
          placeholder="Buscar usuários..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="search-input"
        />
        
        <button
          onClick={fetchUsers}
          disabled={loading}
          className="refresh-button"
        >
          {loading ? 'Carregando...' : 'Atualizar'}
        </button>
      </div>

      <div className="user-list">
        {users.map((user) => (
          <UserCard 
            key={user.id} 
            isActive={user.status === 'active'}
          >
            <h3>{user.name}</h3>
            <p>Email: {user.email}</p>
            <p>Status: {user.status.toUpperCase()}</p>
            <div className="meta">
              <span>Função: {renderUserRole('admin')}</span>
              <button 
                onClick={() => console.log('Edit:', user.id)}
                className="edit-button"
              >
                Editar
              </button>
            </div>
          </UserCard>
        ))}
      </div>
    </div>
  );
};

// Implementação de uso
const App: FC = () => {
  const mockUsers: User[] = [
    {
      id: 1,
      name: 'Alice Silva',
      email: 'alice@example.com',
      status: 'active'
    },
    {
      id: 2,
      name: 'Bob Santos',
      email: 'bob@example.com',
      status: 'inactive'
    }
  ];

  return (
    <div style={{ maxWidth: '800px', margin: '0 auto', padding: '2rem' }}>
      <h1 style={{ color: '#758DC4' }}>Painel de Usuários</h1>
      <UserDashboard initialUsers={mockUsers} />
    </div>
  );
};

export default App;